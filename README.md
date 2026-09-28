# Website Portfolio cá nhân

Đây là bài thực hành môn **Triển khai và Quản trị Hệ thống Phần mềm**. Dự án gồm website portfolio có trang quản trị, MySQL, phpMyAdmin, Nginx, Prometheus, Grafana, Loki và Promtail. Các dịch vụ được khai báo và khởi chạy bằng Docker Compose.

Repository: [github.com/dtc245200102-alt/portfolio-system](https://github.com/dtc245200102-alt/portfolio-system)

Sinh viên thực hiện: **Nguyễn Văn Khánh** · Mã số sinh viên: **DTC245200102**

Website hiện được cấu hình để truy cập trên máy đang chạy Docker Desktop qua `localhost`. Chứng thư HTTPS tự ký phục vụ môi trường thực hành, không phải chứng thư dùng cho website công khai.

## Chức năng

- Khách truy cập xem hồ sơ, học vấn, kỹ năng, dự án và gửi lời nhắn liên hệ.
- Quản trị viên đăng nhập để sửa thông tin hồ sơ và học vấn, thay ảnh, quản lý các mục giới thiệu, thêm hoặc xóa kỹ năng và dự án, xem hoặc xóa lời nhắn.
- Ảnh JPG, PNG và WebP, tối đa 8 MB, được tách nền trên trình duyệt trước khi lưu. Lần đầu cần Internet để tải thư viện và mô hình xử lý ảnh.
- Giao diện có chế độ sáng và tối.

## Các thành phần

| Thành phần | Công việc |
| --- | --- |
| PHP-FPM 8.4 | Xử lý trang portfolio, đăng nhập quản trị và dữ liệu biểu mẫu. |
| MySQL 8.4 | Lưu hồ sơ, các mục giới thiệu, kỹ năng, dự án và lời nhắn. |
| phpMyAdmin 5.2 | Giao diện quản lý MySQL; bật bằng Compose profile `tools`. |
| Nginx | Reverse proxy tới PHP-FPM, chuyển HTTP sang HTTPS và thêm security headers. |
| Prometheus, cAdvisor và exporters | Thu thập số liệu container, Nginx và MySQL. |
| Grafana 12 | Hiển thị dashboard đã cấu hình sẵn từ Prometheus và Loki. |
| Loki và Promtail | Thu thập log ứng dụng, Nginx, MySQL và phục vụ truy vấn LogQL. |

## Yêu cầu để chạy

- Windows với PowerShell.
- Docker Desktop đã cài đặt và đang chạy Linux containers qua WSL 2.
- Git để tải repository.
- Internet trong lần đầu tải image Docker và lần đầu dùng chức năng tách nền ảnh.

## Cài đặt và chạy lần đầu

### 1. Tải mã nguồn

Mở PowerShell tại thư mục muốn lưu dự án:

```powershell
git clone https://github.com/dtc245200102-alt/portfolio-system.git
Set-Location .\portfolio-system
```

Các lệnh Compose bên dưới cần được chạy trong thư mục có file `compose.yaml`.

### 2. Tạo mật khẩu cục bộ

Chạy script tạo các file secrets:

```powershell
Set-ExecutionPolicy -Scope Process Bypass
.\scripts\setup-secrets.ps1
```

Ghi lại mật khẩu Portfolio admin và Grafana mà script in ra. Script chỉ hiện hai mật khẩu này lúc tạo và không ghi đè file secrets đã có. Nếu thư mục `secrets` đã được thiết lập, bỏ qua bước này; không xóa hoặc tạo lại các mật khẩu khi database đang dùng chúng.

Các file trong `secrets/` chứa thông tin đăng nhập cục bộ và được Git bỏ qua. Không đưa các file này lên GitHub.

### 3. Khởi chạy toàn bộ hệ thống

Profile `tools` bật phpMyAdmin để dùng trong phần trình diễn:

```powershell
docker compose --profile tools up -d --build
docker compose ps
```

Lần đầu, Docker cần tải các image và ứng dụng cần thời gian để tạo cơ sở dữ liệu, chạy migration, sinh chứng thư localhost và khởi động các dịch vụ. Có thể dùng `docker compose ps` để xem trạng thái từng container.

### 4. Mở các dịch vụ

| Dịch vụ | Địa chỉ trên máy chạy Docker |
| --- | --- |
| Website | [https://localhost:8443](https://localhost:8443) |
| Trang quản trị | [https://localhost:8443/admin](https://localhost:8443/admin) |
| phpMyAdmin | [http://localhost:8081](http://localhost:8081) |
| Grafana | [http://localhost:3000](http://localhost:3000) |
| Prometheus | [http://localhost:9090](http://localhost:9090) |

Trình duyệt sẽ cảnh báo chứng thư khi mở HTTPS vì chứng thư được tự ký cho `localhost`. Đây là cấu hình của môi trường thực hành trên máy cá nhân.

### 5. Đăng nhập quản trị và công cụ

- **Portfolio admin:** tên đăng nhập `admin`; mật khẩu được tạo ở bước 2. Nếu cần xem lại, mật khẩu nằm trong `secrets/admin_password.txt` trên máy đã chạy script.
- **Grafana:** tên đăng nhập `admin`; mật khẩu nằm trong `secrets/grafana_admin_password.txt`.
- **phpMyAdmin:** máy chủ MySQL được cấu hình là `db`. Dùng database `portfolio`, tài khoản ứng dụng `portfolio_app` và mật khẩu trong `secrets/db_app_password.txt`.

Không chép mật khẩu thật vào README, báo cáo công khai hoặc GitHub.

## Chạy lại sau khi tắt máy

Mở Docker Desktop, chờ Docker Engine chạy, rồi mở PowerShell tại thư mục dự án. Với bản đang đặt ở `D:\portfolio-system`, dùng:

```powershell
Set-Location 'D:\portfolio-system'
docker compose --profile tools up -d
docker compose ps
```

Lệnh này khởi động lại các dịch vụ đã tạo và giữ dữ liệu trong Docker volumes. Không cần tạo lại secrets nếu các file trong `secrets/` vẫn còn.

## Kiểm tra và trình diễn theo tiêu chí đề tài

### Mã nguồn trên GitHub

Repository công khai ở đường dẫn đầu README. Có thể xem các commit bằng:

```powershell
git status --short --branch
git log --oneline --reverse
```

Các commit đầu ghi nhận những mốc chính: ứng dụng PHP/MySQL, Compose cùng Nginx HTTPS, rồi giám sát và hướng dẫn chạy. Các thay đổi tiếp theo được lưu thành những commit riêng trong cùng lịch sử.

Ba commit triển khai ban đầu có nội dung:

- `feat: build portfolio app with MySQL`
- `feat: add Compose deployment and HTTPS reverse proxy`
- `feat: finalize portfolio with monitoring and setup guide`

### Website, trang quản trị và cơ sở dữ liệu

Mở website và trang `/admin`; đăng nhập rồi thử cập nhật một trường hồ sơ. Mở phpMyAdmin để xem database `portfolio` và các bảng `profile`, `about_facts`, `skills`, `projects`, `contact_messages`. `docker compose ps` cho biết trạng thái ứng dụng, web, database và phpMyAdmin.

### Nginx, HTTPS và security headers

Nginx nhận cổng HTTP `8080` rồi chuyển hướng sang HTTPS `8443`. Có thể kiểm tra phản hồi trong PowerShell:

```powershell
curl.exe -I http://localhost:8080
curl.exe -k -I https://localhost:8443
```

Lệnh đầu kiểm tra chuyển hướng; lệnh thứ hai hiển thị các header HTTPS. Cấu hình nằm trong `infra/nginx/default.conf`.

### Prometheus và Grafana

Trong Prometheus, mở **Status → Targets**. Cấu hình có các job `prometheus`, `containers`, `nginx` và `mysql`; kiểm tra trạng thái của từng job tại thời điểm trình diễn. Trong Grafana, dashboard **Portfolio System Overview** được nạp từ cấu hình trong repository. Tên tài khoản là `admin`; dùng mật khẩu đã tạo ở bước 2.

### Loki, Promtail và LogQL

Trong Grafana, chọn **Explore**, datasource **Loki**, rồi chạy các truy vấn sau. Chọn khoảng thời gian phù hợp với lúc hệ thống có log:

```logql
{service="nginx"}
```

Hiển thị log Nginx.

```logql
{service="app"} |= "contact_message_received"
```

Lọc sự kiện sau khi một lời nhắn hợp lệ được gửi từ biểu mẫu liên hệ. Ứng dụng chỉ ghi sự kiện nhận lời nhắn, không ghi nội dung riêng tư của lời nhắn vào log.

```logql
{service="mysql"}
```

Hiển thị log MySQL trong khoảng thời gian đã chọn. Promtail đọc log ứng dụng, Nginx và MySQL từ volume dùng chung rồi gửi tới Loki.

### Hardening

Các cấu hình có thể đối chiếu trong repository:

- Các mạng `application`, `database` và `monitoring` được khai báo nội bộ; các cổng website, phpMyAdmin, Grafana và Prometheus chỉ bind vào loopback `127.0.0.1`.
- Mật khẩu nằm trong Docker secrets ở thư mục `secrets/`; `.gitignore` loại các file này khỏi Git.
- PHP-FPM chạy bằng `www-data`; Nginx dùng image unprivileged.
- Nhiều dịch vụ dùng filesystem chỉ đọc, `no-new-privileges` và loại bỏ Linux capabilities không cần thiết.
- Nginx đặt các header CSP, HSTS, `X-Content-Type-Options`, `X-Frame-Options`, Referrer-Policy và Permissions-Policy.

cAdvisor cần đọc Docker socket để thu thập số liệu container. Socket được gắn chỉ đọc nhưng vẫn là giao diện nhạy cảm; chỉ nên chạy stack này trên máy phát triển đáng tin cậy.

### Ảnh minh chứng cho báo cáo và buổi demo

Sau khi chạy và kiểm tra các bước trên, có thể chụp màn hình website và trang quản trị, database trong phpMyAdmin, phản hồi HTTPS/header của Nginx, Prometheus Targets, dashboard Grafana và kết quả các truy vấn LogQL. Chỉ đưa vào báo cáo những trạng thái đã quan sát được trên máy chạy.

## Lệnh vận hành thường dùng

```powershell
docker compose ps
docker compose logs --tail 100 web app db
docker compose logs --tail 100 loki promtail
docker compose --profile tools down
```

`docker compose down` dừng và gỡ container nhưng giữ dữ liệu trong volumes. Không dùng `docker compose down -v` trừ khi muốn xóa cả dữ liệu MySQL, log, ảnh tải lên, dashboard và chỉ số đã lưu.

Nếu Docker báo không kết nối được daemon, hãy mở Docker Desktop và chờ Docker Engine chạy rồi thử lại. Nếu một dịch vụ không khởi động, xem `docker compose ps` và log của dịch vụ đó trước khi thay đổi cấu hình.

## Bố cục mã nguồn

```text
app/                         Kết nối MySQL, migration, session, CSRF và xử lý route
public/                      Front controller, CSS, JavaScript và tài nguyên giao diện
views/                       Giao diện portfolio và trang quản trị
infra/php/                   Dockerfile, PHP và PHP-FPM config
infra/mysql/                 Schema, tài khoản exporter và cấu hình log MySQL
infra/nginx/                 Reverse proxy, HTTPS và security headers
infra/observability/         Prometheus, Grafana, Loki và Promtail
scripts/                     Script tạo mật khẩu cục bộ
compose.yaml                 Dịch vụ, mạng, volumes và secrets của Docker Compose
```
