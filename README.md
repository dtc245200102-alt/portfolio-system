# Hệ thống Portfolio cá nhân

Website giới thiệu cá nhân có khu vực quản trị nội dung, MySQL/phpMyAdmin, Nginx HTTPS, Prometheus/Grafana và Loki/Promtail. Các dịch vụ chạy bằng Docker Compose; mật khẩu và chứng thư tự ký được tạo riêng trên máy chạy hệ thống.

Portfolio đã được điền sẵn tên Nguyễn Văn Khánh và mã sinh viên DTC245200102. Bạn có thể sửa các thông tin này trong trang **Quản trị → Thông tin cá nhân**.

## 1. Thành phần

| Thành phần | Vai trò |
| --- | --- |
| PHP-FPM 8.4 | Trang portfolio, đăng nhập quản trị, CRUD nội dung và xử lý liên hệ |
| MySQL 8.4 | Lưu hồ sơ, kỹ năng, dự án và tin nhắn |
| phpMyAdmin | Quản lý cơ sở dữ liệu qua trình duyệt |
| Nginx | Reverse proxy tới PHP-FPM, chuyển HTTP sang HTTPS và thêm security headers |
| Prometheus + cAdvisor + exporters | Thu thập số liệu container, Nginx và MySQL |
| Grafana | Dashboard dựng sẵn cho CPU/bộ nhớ container, Nginx, MySQL và log |
| Loki + Promtail | Thu thập log ứng dụng, Nginx và MySQL để truy vấn bằng LogQL |

## 2. Chạy lần đầu trên Windows

1. Cài Docker Desktop, bật Linux containers/WSL 2 và mở Docker Desktop.
2. Mở PowerShell tại thư mục chứa `compose.yaml`.
3. Tạo mật khẩu ngẫu nhiên cho cơ sở dữ liệu, tài khoản quản trị và Grafana:

   ```powershell
   Set-ExecutionPolicy -Scope Process Bypass
   .\scripts\setup-secrets.ps1
   ```

   Ghi lại mật khẩu Portfolio admin và Grafana được in ra. Các file trong `secrets/` chứa mật khẩu thật và đã được loại khỏi Git.

4. Khởi động website cùng phpMyAdmin:

   ```powershell
   docker compose --profile tools up -d --build
   ```

5. Mở các địa chỉ sau:

   - Website: [https://localhost:8443](https://localhost:8443)
   - Trang quản trị: [https://localhost:8443/admin](https://localhost:8443/admin)
   - phpMyAdmin: [http://localhost:8081](http://localhost:8081)
   - Grafana: [http://localhost:3000](http://localhost:3000)
   - Prometheus: [http://localhost:9090](http://localhost:9090)

   Chứng thư HTTPS được tự tạo để thực hành nên trình duyệt sẽ báo chưa tin cậy. Xác nhận tiếp tục tới `localhost`; không dùng chứng thư này cho website công khai.

6. Đăng nhập trang quản trị với tên `admin` và mật khẩu vừa được in ra. Sửa hồ sơ, thêm hoặc xóa kỹ năng/dự án, rồi gửi thử một lời nhắn ở website để xem tin nhắn trong mục **Hộp thư**.

7. Đăng nhập Grafana với tên `admin` và mật khẩu Grafana đã được in ra. Dashboard **Portfolio System Overview** được nạp tự động. Trong phpMyAdmin, chọn máy chủ `db`, database `portfolio`, rồi đăng nhập bằng `portfolio_app` và mật khẩu trong `secrets/db_app_password.txt`.

## 3. Kiểm tra các phần của đề tài

### Nginx và HTTPS

Truy cập `http://localhost:8080` để xem Nginx chuyển hướng sang HTTPS. Các header được cấu hình trong `infra/nginx/default.conf`: CSP, HSTS, `X-Content-Type-Options`, `X-Frame-Options`, Referrer-Policy và Permissions-Policy.

### Prometheus và Grafana

Trong Prometheus, mở **Status → Targets**. Các job `containers`, `nginx` và `mysql` cần ở trạng thái `UP`. Dashboard Grafana có số liệu CPU/bộ nhớ từng container, lưu lượng Nginx, kết nối MySQL và log Nginx.

### LogQL trong Grafana Explore

Chọn datasource **Loki** rồi chạy từng truy vấn:

```logql
{service="nginx"}
```

```logql
{service="app"} |= "contact_message_received"
```

```logql
{service="mysql"}
```

Lời nhắn của người dùng không được ghi vào log ứng dụng; chỉ ghi sự kiện nhận lời nhắn để tránh lộ nội dung cá nhân.

### Các lệnh Docker thường dùng

```powershell
docker compose ps
docker compose logs -f web app db
docker compose logs -f loki promtail
docker compose down
```

Muốn chạy các dịch vụ mặc định mà không có phpMyAdmin, bỏ `--profile tools`. Dữ liệu MySQL, dashboard và log được lưu trong Docker volumes. `docker compose down -v` xóa các volumes đó, gồm toàn bộ dữ liệu; chỉ chạy khi muốn tạo lại hệ thống từ đầu.

## 4. Bố cục mã nguồn

```text
app/                         Kết nối MySQL, session, CSRF và xử lý route
public/                      Front controller và tài nguyên CSS/JavaScript
views/                       Giao diện website và trang quản trị
infra/php/                   Dockerfile, PHP và PHP-FPM config
infra/mysql/                 Schema, init user exporter và cấu hình log MySQL
infra/nginx/                 Reverse proxy, HTTPS và security headers
infra/observability/         Prometheus, Grafana, Loki và Promtail
scripts/                     Script tạo mật khẩu cục bộ
compose.yaml                 Khai báo dịch vụ, networks, volumes và secrets
```

PHP dùng PDO prepared statements, escape dữ liệu đầu ra, session cookie HttpOnly/SameSite, đổi session ID sau đăng nhập, token CSRF, giới hạn thử đăng nhập và kiểm tra đường dẫn URL. Tài khoản `portfolio_app` chỉ có quyền trên database `portfolio`; exporter MySQL có user riêng với quyền đọc số liệu. Mạng database và ứng dụng được đánh dấu `internal`; chỉ website, Grafana, Prometheus và phpMyAdmin có cổng được công bố trên loopback.

`cAdvisor` cần đọc Docker socket để liệt kê và lấy số liệu container. Socket được gắn `:ro`, dịch vụ không công bố cổng ra máy host và không có capability bổ sung; Docker socket vẫn là giao diện có quyền cao, nên chỉ bật stack này trên máy phát triển đáng tin cậy. Nginx, PHP, MySQL và các dịch vụ quan sát còn lại dùng `no-new-privileges`, filesystem read-only khi phù hợp và capability đã loại bỏ.

## 5. Tình trạng Promtail

Đề tài yêu cầu cụ thể Loki + Promtail, vì vậy Compose có Promtail và cấu hình LogQL để đáp ứng tiêu chí thực hành. Theo [tài liệu Grafana](https://grafana.com/docs/loki/latest/send-data/promtail/), Promtail đã EOL từ ngày 02/03/2026 và không còn nhận cập nhật; Grafana hướng người dùng sang [Grafana Alloy](https://grafana.com/docs/alloy/latest/set-up/migrate/). Không dùng Promtail cho hệ thống công khai mới. Có thể chuyển các scrape job trong `infra/observability/promtail.yml` sang Alloy sau khi hoàn thành phần trình diễn theo yêu cầu môn học.

## 6. Đưa mã nguồn lên GitHub

Repository là dự án được Git theo dõi: source code, cấu hình và lịch sử commit. Thư mục `portfolio-system` trên máy là repository cục bộ; GitHub lưu một bản trực tuyến để nộp bài và chia sẻ. Thư mục này đã có 3 commit cục bộ với nội dung riêng cho ứng dụng/database, Nginx/Compose và logging/giám sát.

Tạo tài khoản GitHub theo hướng dẫn môn học bằng mã sinh viên `DTC245200102`, tạo repository tên `portfolio-system`, rồi tại PowerShell trong thư mục project trỏ repo cục bộ tới GitHub. Không đưa thư mục `secrets/` lên Git.

```powershell
git log --oneline -3
git remote add origin https://github.com/DTC245200102/portfolio-system.git
git push -u origin main
```

Nếu GitHub không cho đăng ký đúng tên tài khoản này hoặc bạn đã có username khác, thay phần `DTC245200102` bằng username thực tế. Nếu repository đã có remote tên `origin`, dùng `git remote set-url origin <URL>` thay cho lệnh `git remote add`. Xem `git status` trước mỗi lần push để chắc chắn không có file mật khẩu nào được theo dõi.
