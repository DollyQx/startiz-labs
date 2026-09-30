# Security Policy

## Reporting Vulnerabilities

If you identify any security issue within `startiz-labs`, please notify the security team directly.

## Security Practices & Architecture Controls

1. **Environment & Secret Protection**:
   - Environment secrets (`APP_KEY`, database credentials, Razorpay secret keys) must reside in `.env` files and never be checked into version control.

2. **Cross-Site Request Forgery (CSRF)**:
   - All HTTP POST/PUT/DELETE forms and AJAX requests are verified using Laravel CSRF middleware tokens.

3. **Payment Security & Price Integrity**:
   - Payment order creation derives transaction amounts strictly server-side from stored database model values rather than client parameters.

4. **Session & Authentication**:
   - Secure HTTP-only cookies with session encryption and CSRF protection are enforced across all web routes.
