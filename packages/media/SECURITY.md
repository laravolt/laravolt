# Security Considerations for Direct Upload

This document outlines security considerations and best practices for using the Direct Upload feature.

## Security Features

### 1. File Validation

#### Size Validation
The component enforces file size limits both client-side (UI) and server-side (validation):

```php
// Dynamic validation based on configuration
$this->validate([
    'file' => ['required', 'file', 'max:' . $this->maxFileSize],
]);
```

**Recommendations:**
- Set appropriate `maxFileSize` based on your use case
- Consider different limits for different collections
- Monitor upload patterns for abuse

#### Type Validation
Configure allowed MIME types in `config/direct-upload.php`:

```php
'allowed_mime_types' => [
    'image/jpeg',
    'image/png',
    'application/pdf',
    // Add only necessary types
],
```

**Recommendations:**
- Restrict to necessary file types only
- Never allow executable files (.exe, .sh, .bat)
- Be cautious with script files (.php, .js, .html)

### 2. Authentication & Authorization

#### Guest Upload Support
By default, the component supports both authenticated and guest users:

```php
$user = auth()->user() ?? Guest::first();
```

**Recommendations:**
- For sensitive data, require authentication:
  ```php
  'security' => [
      'require_auth' => true,
  ],
  ```
- Implement additional authorization checks for specific collections
- Consider rate limiting for guest uploads

#### Collection-Based Access Control
Implement custom authorization logic:

```php
public function updatedFile()
{
    // Check if user can upload to this collection
    if (!auth()->user()->can('upload-to-' . $this->collection)) {
        $this->addError('file', 'Unauthorized');
        return;
    }
    
    // Continue with upload...
}
```

### 3. Storage Security

#### S3 Bucket Configuration

**Public Buckets:**
- Use for public assets only
- Configure appropriate CORS rules
- Set bucket policies carefully

**Private Buckets:**
- Use for sensitive data
- Generate signed URLs for access
- Implement appropriate IAM policies

**Example S3 Bucket Policy:**
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::your-bucket/*"
        }
    ]
}
```

**Example IAM Policy:**
```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject"
            ],
            "Resource": "arn:aws:s3:::your-bucket/*"
        }
    ]
}
```

#### CORS Configuration
Ensure proper CORS settings for your S3 bucket:

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
        "AllowedOrigins": ["https://yourdomain.com"],
        "ExposeHeaders": ["ETag"]
    }
]
```

**Recommendations:**
- Don't use wildcard (`*`) for AllowedOrigins in production
- Only allow necessary HTTP methods
- Review and update CORS rules regularly

### 4. Rate Limiting

Built-in rate limiting configuration:

```php
'security' => [
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 60, // uploads per hour
        'decay_minutes' => 60,
    ],
],
```

**Recommendations:**
- Enable rate limiting in production
- Adjust limits based on legitimate use patterns
- Monitor for abuse and adjust as needed
- Consider different limits for authenticated vs guest users

### 5. Error Handling

The component includes comprehensive error handling:

```php
try {
    // Upload logic
} catch (FileCannotBeAdded $e) {
    $this->addError('file', $e->getMessage());
    report($e);
} catch (\Exception $e) {
    $this->addError('file', 'Failed to upload file: ' . $e->getMessage());
    report($e);
}
```

**Recommendations:**
- Never expose sensitive error details to users
- Log all errors for monitoring
- Set up alerts for unusual error patterns
- Sanitize error messages shown to users

## Security Checklist

### Before Deployment

- [ ] Review and set appropriate `maxFileSize` limits
- [ ] Configure `allowed_mime_types` to only necessary types
- [ ] Set `require_auth` to `true` for sensitive uploads
- [ ] Configure S3 bucket policies correctly
- [ ] Set up proper CORS configuration
- [ ] Enable rate limiting
- [ ] Configure IAM policies with least privilege
- [ ] Test file upload with various file types
- [ ] Verify unauthorized users cannot upload
- [ ] Test rate limiting functionality

### After Deployment

- [ ] Monitor upload patterns and file sizes
- [ ] Review error logs regularly
- [ ] Check for unauthorized access attempts
- [ ] Audit S3 bucket permissions periodically
- [ ] Monitor storage costs and usage
- [ ] Update security configurations as needed
- [ ] Test disaster recovery procedures

## Common Security Pitfalls

### 1. Unrestricted File Types
❌ **Don't:**
```php
'allowed_mime_types' => null, // Allows all file types
```

✅ **Do:**
```php
'allowed_mime_types' => [
    'image/jpeg',
    'image/png',
    'application/pdf',
],
```

### 2. No Authentication
❌ **Don't:**
```php
'require_auth' => false, // For sensitive data
```

✅ **Do:**
```php
'require_auth' => true, // For sensitive uploads
```

### 3. Wildcard CORS
❌ **Don't:**
```json
"AllowedOrigins": ["*"]
```

✅ **Do:**
```json
"AllowedOrigins": ["https://yourdomain.com"]
```

### 4. No Rate Limiting
❌ **Don't:**
```php
'rate_limit' => [
    'enabled' => false,
],
```

✅ **Do:**
```php
'rate_limit' => [
    'enabled' => true,
    'max_attempts' => 60,
],
```

### 5. Public Bucket for Sensitive Data
❌ **Don't:**
```php
's3' => [
    'public' => true, // For private documents
],
```

✅ **Do:**
```php
's3' => [
    'public' => false, // Use signed URLs for access
],
```

## Monitoring and Alerts

### What to Monitor

1. **Upload Volume**
   - Sudden spikes in uploads
   - Unusual file sizes
   - Failed upload attempts

2. **Storage Usage**
   - Disk space growth rate
   - Unusual storage patterns
   - Cost increases

3. **Error Rates**
   - Failed validations
   - S3 errors
   - Permission denied errors

4. **User Patterns**
   - Guest vs authenticated uploads
   - Upload times and frequencies
   - File types being uploaded

### Setting Up Alerts

```php
// In your monitoring service or custom middleware
if ($uploadCount > $threshold) {
    // Send alert
    Mail::to('admin@example.com')
        ->send(new UploadAlert($uploadCount));
}
```

## Incident Response

### If Malicious Files Are Uploaded

1. **Immediate Actions:**
   - Identify and delete malicious files
   - Block the source IP/user
   - Review upload logs

2. **Investigation:**
   - Determine scope of breach
   - Check for similar uploads
   - Review access logs

3. **Prevention:**
   - Update validation rules
   - Strengthen authentication
   - Review and update monitoring

### If Storage Is Compromised

1. **Immediate Actions:**
   - Rotate credentials
   - Review IAM policies
   - Check for unauthorized access

2. **Recovery:**
   - Restore from backups if needed
   - Verify data integrity
   - Update security configurations

3. **Documentation:**
   - Document the incident
   - Update security procedures
   - Train team on lessons learned

## Data Protection

### GDPR Compliance

If handling EU user data:

1. **Right to Access:** Provide users access to their uploaded files
2. **Right to Deletion:** Implement file deletion functionality (already included)
3. **Data Minimization:** Only store necessary metadata
4. **Data Portability:** Allow users to export their files

### Encryption

#### At Rest
S3 provides server-side encryption options:

```php
// In your filesystems.php
's3' => [
    // ... other config
    'encryption' => 'AES256', // or 'aws:kms'
],
```

#### In Transit
Always use HTTPS for file transfers (enabled by default in S3).

### Retention Policies

Implement automatic deletion of old uploads:

```php
// In a scheduled job
Schedule::command('media:cleanup-old')
    ->daily()
    ->when(function () {
        return config('direct-upload.retention.enabled');
    });
```

## Best Practices Summary

1. **Validate Everything:** File type, size, user permissions
2. **Least Privilege:** IAM policies should grant minimum necessary access
3. **Monitor Actively:** Set up alerts for unusual patterns
4. **Encrypt Data:** Use encryption at rest and in transit
5. **Audit Regularly:** Review configurations and logs
6. **Keep Updated:** Update dependencies and security configurations
7. **Document Everything:** Maintain security documentation
8. **Train Team:** Ensure team understands security implications

## Resources

- [AWS S3 Security Best Practices](https://docs.aws.amazon.com/AmazonS3/latest/userguide/security-best-practices.html)
- [Livewire Security](https://livewire.laravel.com/docs/security)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [OWASP File Upload Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html)

## Reporting Security Issues

If you discover a security vulnerability in this package:

1. **Do NOT** open a public issue
2. Email the maintainers directly
3. Include detailed information about the vulnerability
4. Allow reasonable time for a fix before public disclosure

---

**Remember:** Security is an ongoing process, not a one-time setup. Regularly review and update your security configurations.
