(() => {
    const VISION_VERSION = '0.10.21';
    const WASM_PATH = `https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@${VISION_VERSION}/wasm`;
    const MODEL_PATH = 'https://storage.googleapis.com/mediapipe-models/image_segmenter/selfie_segmenter/float16/latest/selfie_segmenter.tflite';
    const MAX_UPLOAD_BYTES = 8 * 1024 * 1024;
    const MAX_IMAGE_EDGE = 1500;
    const CACHE_NAME = 'portfolio-portrait-cutouts-v1';
    let segmenterPromise;
    let cachePromise;

    const openCache = () => {
        if (!('indexedDB' in window)) return Promise.resolve(null);
        if (!cachePromise) {
            cachePromise = new Promise((resolve) => {
                const request = indexedDB.open(CACHE_NAME, 1);
                request.onupgradeneeded = () => request.result.createObjectStore('images');
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => resolve(null);
            });
        }
        return cachePromise;
    };

    const readCachedCutout = async (key) => {
        const database = await openCache();
        if (!database) return null;
        return new Promise((resolve) => {
            const request = database.transaction('images', 'readonly').objectStore('images').get(key);
            request.onsuccess = () => resolve(request.result instanceof Blob ? request.result : null);
            request.onerror = () => resolve(null);
        });
    };

    const saveCachedCutout = async (key, blob) => {
        const database = await openCache();
        if (!database) return;
        await new Promise((resolve) => {
            const request = database.transaction('images', 'readwrite').objectStore('images').put(blob, key);
            request.onsuccess = request.onerror = () => resolve();
        });
    };

    const getSegmenter = () => {
        if (!segmenterPromise) {
            segmenterPromise = (async () => {
                const vision = await import(`https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@${VISION_VERSION}/vision_bundle.mjs`);
                const fileset = await vision.FilesetResolver.forVisionTasks(WASM_PATH);
                const options = {
                    baseOptions: { modelAssetPath: MODEL_PATH, delegate: 'GPU' },
                    runningMode: 'IMAGE',
                    outputCategoryMask: false,
                    outputConfidenceMasks: true
                };
                try {
                    return await vision.ImageSegmenter.createFromOptions(fileset, options);
                } catch (_) {
                    options.baseOptions.delegate = 'CPU';
                    return vision.ImageSegmenter.createFromOptions(fileset, options);
                }
            })().catch((error) => {
                segmenterPromise = null;
                throw error;
            });
        }
        return segmenterPromise;
    };

    const segment = async (canvas) => {
        const segmenter = await getSegmenter();
        return new Promise((resolve, reject) => {
            try {
                segmenter.segment(canvas, resolve);
            } catch (error) {
                reject(error);
            }
        });
    };

    const smoothStep = (edge0, edge1, value) => {
        const amount = Math.max(0, Math.min(1, (value - edge0) / (edge1 - edge0)));
        return amount * amount * (3 - 2 * amount);
    };

    const toPngBlob = (canvas) => new Promise((resolve, reject) => {
        canvas.toBlob((blob) => blob ? resolve(blob) : reject(new Error('Không thể tạo ảnh PNG.')), 'image/png');
    });

    async function removeBackground(file) {
        const bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });
        const scale = Math.min(1, MAX_IMAGE_EDGE / Math.max(bitmap.width, bitmap.height));
        const width = Math.max(1, Math.round(bitmap.width * scale));
        const height = Math.max(1, Math.round(bitmap.height * scale));
        const source = document.createElement('canvas');
        source.width = width;
        source.height = height;
        source.getContext('2d', { willReadFrequently: true }).drawImage(bitmap, 0, 0, width, height);
        bitmap.close();

        const result = await segment(source);
        const mask = result.confidenceMasks?.[0];
        if (!mask) throw new Error('Không nhận được mặt nạ tách nền từ ảnh.');

        const maskValues = mask.getAsFloat32Array();
        const maskWidth = mask.width;
        const maskHeight = mask.height;
        const maskCanvas = document.createElement('canvas');
        maskCanvas.width = maskWidth;
        maskCanvas.height = maskHeight;
        const maskContext = maskCanvas.getContext('2d', { willReadFrequently: true });
        const maskPixels = maskContext.createImageData(maskWidth, maskHeight);

        for (let pixel = 0; pixel < maskValues.length; pixel += 1) {
            const alpha = Math.round(smoothStep(0.18, 0.68, maskValues[pixel]) * 255);
            const offset = pixel * 4;
            maskPixels.data[offset] = 255;
            maskPixels.data[offset + 1] = 255;
            maskPixels.data[offset + 2] = 255;
            maskPixels.data[offset + 3] = alpha;
        }
        maskContext.putImageData(maskPixels, 0, 0);
        mask.close?.();
        result.close?.();

        const output = document.createElement('canvas');
        output.width = width;
        output.height = height;
        const outputContext = output.getContext('2d', { willReadFrequently: true });
        outputContext.drawImage(source, 0, 0);
        outputContext.globalCompositeOperation = 'destination-in';
        outputContext.filter = 'blur(1.1px)';
        outputContext.drawImage(maskCanvas, 0, 0, width, height);
        outputContext.filter = 'none';
        outputContext.globalCompositeOperation = 'source-over';

        const pixels = outputContext.getImageData(0, 0, width, height).data;
        let minX = width;
        let minY = height;
        let maxX = -1;
        let maxY = -1;
        for (let y = 0; y < height; y += 2) {
            for (let x = 0; x < width; x += 2) {
                if (pixels[(y * width + x) * 4 + 3] > 20) {
                    minX = Math.min(minX, x);
                    minY = Math.min(minY, y);
                    maxX = Math.max(maxX, x);
                    maxY = Math.max(maxY, y);
                }
            }
        }

        if (maxX < minX || maxY < minY) throw new Error('Không tách được người trong ảnh. Hãy chọn ảnh chân dung sáng, rõ hơn.');
        const padding = Math.round(Math.max(maxX - minX + 1, maxY - minY + 1) * 0.055);
        const left = Math.max(0, minX - padding);
        const top = Math.max(0, minY - padding);
        const right = Math.min(width, maxX + padding + 1);
        const bottom = Math.min(height, maxY + padding + 1);
        const cropped = document.createElement('canvas');
        cropped.width = right - left;
        cropped.height = bottom - top;
        cropped.getContext('2d').drawImage(output, left, top, cropped.width, cropped.height, 0, 0, cropped.width, cropped.height);
        return toPngBlob(cropped);
    }

    const makeObjectUrl = (blob) => URL.createObjectURL(blob);

    const homepageImages = document.querySelectorAll('img[data-cutout-on-view="true"]');
    homepageImages.forEach((image) => {
        const sourceUrl = image.currentSrc || image.src;
        const cacheKey = `${location.origin}${sourceUrl}`;
        image.dataset.cutoutOnView = 'processing';
        (async () => {
            let cutout = await readCachedCutout(cacheKey);
            if (!cutout) {
                const response = await fetch(sourceUrl, { credentials: 'same-origin' });
                if (!response.ok) throw new Error('Không tải được ảnh hồ sơ.');
                const original = await response.blob();
                cutout = await removeBackground(new File([original], 'portrait', { type: original.type }));
                await saveCachedCutout(cacheKey, cutout);
            }
            image.src = makeObjectUrl(cutout);
            image.dataset.cutoutOnView = 'done';
        })().catch((error) => {
            image.dataset.cutoutOnView = 'failed';
            console.warn('Không thể tự tách nền ảnh hồ sơ:', error);
        });
    });

    const uploadForm = document.querySelector('[data-background-removal-form]');
    if (!uploadForm) return;

    const fileInput = uploadForm.querySelector('input[type="file"]');
    const status = uploadForm.querySelector('[data-avatar-status]');
    const preview = uploadForm.querySelector('[data-avatar-preview]');
    const submitButton = uploadForm.querySelector('button[type="submit"]');
    let processedFile = null;
    let previewUrl = '';
    let processingPromise = null;
    let selectionNumber = 0;

    const setStatus = (message, state = '') => {
        if (!status) return;
        status.textContent = message;
        status.dataset.state = state;
    };

    const processSelection = () => {
        const file = fileInput?.files?.[0];
        const currentSelection = ++selectionNumber;
        processedFile = null;
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = '';
        if (preview) preview.hidden = true;
        if (!file) return Promise.resolve(null);
        if (file.size > MAX_UPLOAD_BYTES) {
            setStatus('Ảnh gốc phải nhỏ hơn 8 MB. Hãy chọn ảnh khác.', 'error');
            return Promise.resolve(null);
        }

        setStatus('Đang tách nền trên thiết bị của bạn… Lần đầu có thể cần tải mô hình xử lý ảnh.', 'working');
        if (submitButton) submitButton.disabled = true;
        const task = removeBackground(file).then((blob) => {
            if (currentSelection !== selectionNumber) return null;
            if (blob.size > MAX_UPLOAD_BYTES) throw new Error('Ảnh sau khi tách nền vượt quá 8 MB. Hãy chọn ảnh có độ phân giải thấp hơn.');
            processedFile = new File([blob], 'portrait-cutout.png', { type: 'image/png' });
            previewUrl = makeObjectUrl(blob);
            if (preview) {
                preview.src = previewUrl;
                preview.hidden = false;
            }
            setStatus('Đã tách nền. Ảnh gốc không được gửi đi; xem trước rồi bấm “Cập nhật ảnh”.', 'success');
            return processedFile;
        }).catch((error) => {
            if (currentSelection === selectionNumber) {
                setStatus(`${error.message || 'Không thể tách nền.'} Kiểm tra kết nối Internet rồi thử lại.`, 'error');
            }
            return null;
        }).finally(() => {
            if (currentSelection === selectionNumber) {
                if (!processedFile) processingPromise = null;
                if (submitButton) submitButton.disabled = false;
            }
        });
        processingPromise = task;
        return task;
    };

    fileInput?.addEventListener('change', processSelection);
    uploadForm.addEventListener('submit', async (event) => {
        if (event.submitter?.name === 'remove_avatar') return;
        event.preventDefault();
        let readyFile = processedFile;
        if (!readyFile && fileInput?.files?.length) readyFile = processingPromise ? await processingPromise : await processSelection();
        if (!readyFile) {
            setStatus('Chưa có ảnh đã tách nền. Chọn ảnh và chờ xử lý xong trước khi lưu.', 'error');
            return;
        }

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Đang lưu ảnh…';
        }
        try {
            const data = new FormData(uploadForm);
            data.set('avatar', readyFile, readyFile.name);
            const response = await fetch(uploadForm.action, { method: 'POST', body: data, credentials: 'same-origin', redirect: 'follow' });
            if (!response.ok) throw new Error('Máy chủ chưa lưu được ảnh.');
            window.location.assign(response.url || '/admin');
        } catch (error) {
            setStatus(`${error.message || 'Không thể lưu ảnh.'} Vui lòng thử lại.`, 'error');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Cập nhật ảnh';
            }
        }
    });
})();
