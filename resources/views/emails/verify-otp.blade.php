<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực mã OTP - Rèm Online</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 580px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 30px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #d97706;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #94a3b8;
        }
        .body {
            padding: 32px 28px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .description {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 24px;
        }
        .otp-box {
            background-color: #fef3c7;
            border: 2px dashed #f59e0b;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #92400e;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 38px;
            font-weight: 800;
            color: #b45309;
            letter-spacing: 10px;
            margin: 4px 0;
        }
        .otp-expiry {
            font-size: 12px;
            color: #b45309;
            margin-top: 8px;
        }
        .security-note {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 12px 16px;
            font-size: 13px;
            color: #475569;
            border-radius: 4px;
            margin-bottom: 24px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        .footer a {
            color: #d97706;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RÈM ONLINE</h1>
            <p>Thương Hiệu May Đo Rèm Cửa & Nội Thất Cao Cấp</p>
        </div>
        <div class="body">
            <div class="greeting">Xin chào {{ $user->name }},</div>
            <div class="description">
                Cảm ơn bạn đã đăng ký tài khoản tại <strong>Rèm Online</strong>. Để hoàn tất kích hoạt tài khoản và bảo mật các đơn hàng may đo của bạn, vui lòng nhập mã xác thực OTP dưới đây:
            </div>
            
            <div class="otp-box">
                <div class="otp-label">MÃ XÁC THỰC CỦA BẠN</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">Hiệu lực trong vòng <strong>10 phút</strong></div>
            </div>

            <div class="security-note">
                <strong>Lưu ý bảo mật:</strong> Tuyệt đối không chia sẻ mã này cho bất kỳ ai. Nhân viên Rèm Online không bao giờ yêu cầu cung cấp mã OTP của quý khách.
            </div>

            <div class="description" style="font-size: 13px; color: #6b7280;">
                Nếu bạn không thực hiện yêu cầu đăng ký này, vui lòng bỏ qua email hoặc liên hệ với chúng tôi để được hỗ trợ.
            </div>
        </div>
        <div class="footer">
            © 2026 Rèm Online. Hệ thống may đo và phân phối rèm cửa theo kiến trúc hướng dịch vụ.<br>
            Hotline hỗ trợ: <a href="tel:19008899">1900 8899</a> | Email: <a href="mailto:support@remonline.vn">support@remonline.vn</a>
        </div>
    </div>
</body>
</html>
