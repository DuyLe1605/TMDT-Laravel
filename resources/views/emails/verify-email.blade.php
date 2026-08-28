<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực địa chỉ Email - Aurelia Bags</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #0f172a;
        }
        table {
            border-collapse: collapse;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f6f8;
            padding: 40px 15px;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }
        .email-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 32px 30px;
            text-align: center;
        }
        .brand-name {
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-subtext {
            color: #e0e7ff;
            font-size: 13px;
            margin-top: 6px;
            margin-bottom: 0;
            font-weight: 500;
        }
        .email-body {
            padding: 36px 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .message-text {
            font-size: 15px;
            line-height: 1.65;
            color: #475569;
            margin-top: 0;
            margin-bottom: 24px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }
        .security-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }
        .security-text {
            font-size: 13px;
            color: #64748b;
            line-height: 1.55;
            margin: 0;
        }
        .fallback-text {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
            word-break: break-all;
            margin-top: 24px;
            border-top: 1px solid #f1f5f9;
            padding-top: 18px;
        }
        .fallback-link {
            color: #4f46e5;
            text-decoration: underline;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 32px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1 class="brand-name">AURELIA BAGS</h1>
                <p class="brand-subtext">Hệ Thống Thương Mại Điện Tử Túi Xách Nữ Cao Cấp</p>
            </div>

            <!-- Body -->
            <div class="email-body">
                <h2 class="greeting">Xin chào {{ $user->name ?? 'quý khách' }} 👋</h2>
                <p class="message-text">
                    Cảm ơn bạn đã đăng ký tài khoản tại <strong>Aurelia Bags</strong>. Để bắt đầu trải nghiệm mua sắm và bảo mật tài khoản, vui lòng nhấn vào nút bên dưới để xác thực địa chỉ email của bạn:
                </p>

                <!-- Call To Action Button -->
                <div class="btn-wrapper">
                    <a href="{{ $url }}" target="_blank" class="btn-primary">
                        ✓ Kích hoạt & Xác thực Email
                    </a>
                </div>

                <!-- Security Note -->
                <div class="security-box">
                    <p class="security-text">
                        🔒 <strong>Lưu ý bảo mật:</strong> Liên kết xác thực này có hiệu lực trong vòng <strong>60 phút</strong>. Nếu bạn không thực hiện đăng ký tài khoản này, xin vui lòng bỏ qua email.
                    </p>
                </div>

                <!-- Fallback URL -->
                <p class="fallback-text">
                    Nếu nút bấm trên không hoạt động, bạn có thể sao chép và dán trực tiếp đường dẫn sau vào trình duyệt web:<br>
                    <a href="{{ $url }}" class="fallback-link">{{ $url }}</a>
                </p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p class="footer-text"><strong>Aurelia Bags &bull; E-Commerce Platform</strong></p>
                <p class="footer-text">Email tự động từ hệ thống &bull; Vui lòng không trả lời thư này.</p>
                <p class="footer-text">&copy; {{ date('Y') }} Aurelia Bags. Bảo lưu mọi quyền.</p>
            </div>
        </div>
    </div>
</body>
</html>
