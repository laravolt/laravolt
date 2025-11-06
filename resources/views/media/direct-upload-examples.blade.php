<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Direct Upload Examples - Laravolt Media</title>
    @livewireStyles
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #f5f7fa;
        }
        
        h1 {
            color: #2d3748;
            border-bottom: 3px solid #4299e1;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
        }
        
        h2 {
            color: #2d3748;
            margin-top: 3rem;
            margin-bottom: 1rem;
            font-size: 1.75rem;
        }
        
        .intro {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .intro p {
            margin: 0.5rem 0;
        }
        
        .intro ul {
            margin: 1rem 0;
            padding-left: 2rem;
        }
        
        .intro li {
            margin: 0.5rem 0;
        }
        
        .example {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .example h3 {
            margin-top: 0;
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .example .description {
            color: #718096;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
            overflow-x: auto;
        }
        
        .code-block pre {
            margin: 0;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-info {
            background-color: #bee3f8;
            border-left: 4px solid #4299e1;
            color: #2c5282;
        }
        
        .alert-success {
            background-color: #c6f6d5;
            border-left: 4px solid #48bb78;
            color: #22543d;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .feature-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card h4 {
            margin-top: 0;
            color: #4299e1;
        }
    </style>
</head>
<body>
    <h1>🚀 Direct Upload to Cloud Storage - Examples</h1>
    
    <div class="intro">
        <h3>Welcome to Direct Upload!</h3>
        <p>
            This page demonstrates the new <strong>Direct Upload</strong> feature for Laravolt Media, 
            which enables uploading files directly to cloud storage (like S3) without relying on 
            temporary local storage, making it perfect for cloud-native architectures.
        </p>
        <ul>
            <li>✅ Cloud-compatible - No local temporary storage required</li>
            <li>✅ Livewire 3.x powered - Modern, reactive interface</li>
            <li>✅ S3 compatible - Works with AWS S3, DigitalOcean Spaces, and more</li>
            <li>✅ Easy to use - Just a single Livewire component</li>
        </ul>
    </div>

    <div class="alert alert-info">
        <strong>📝 Note:</strong> Make sure you have configured your S3 credentials in <code>.env</code> 
        and set up the <code>s3</code> disk in <code>config/filesystems.php</code> before using these examples.
    </div>

    <h2>Example 1: Basic Upload to S3</h2>
    <div class="example">
        <h3>Simple File Upload</h3>
        <p class="description">
            The most basic usage - upload any file directly to your S3 bucket with default settings.
        </p>
        
        <livewire:media::direct-upload />
        
        <div class="code-block">
            <pre>&lt;livewire:media::direct-upload /&gt;</pre>
        </div>
    </div>

    <h2>Example 2: Upload to Public Disk</h2>
    <div class="example">
        <h3>Upload to Local Public Storage</h3>
        <p class="description">
            For development or when you want to use local storage instead of S3.
        </p>
        
        <livewire:media::direct-upload 
            disk="public" 
        />
        
        <div class="code-block">
            <pre>&lt;livewire:media::direct-upload 
    disk="public" 
/&gt;</pre>
        </div>
    </div>

    <h2>Example 3: Custom Collection</h2>
    <div class="example">
        <h3>Documents Collection</h3>
        <p class="description">
            Upload files to a specific media collection for better organization.
        </p>
        
        <livewire:media::direct-upload 
            disk="public"
            collection="documents" 
        />
        
        <div class="code-block">
            <pre>&lt;livewire:media::direct-upload 
    disk="public"
    collection="documents" 
/&gt;</pre>
        </div>
    </div>

    <h2>Example 4: Custom File Size Limit</h2>
    <div class="example">
        <h3>Small Files Only (10MB max)</h3>
        <p class="description">
            Restrict uploads to smaller files by setting a custom max file size (in kilobytes).
        </p>
        
        <livewire:media::direct-upload 
            disk="public"
            collection="small-files"
            :max-file-size="10240"
        />
        
        <div class="code-block">
            <pre>&lt;livewire:media::direct-upload 
    disk="public"
    collection="small-files"
    :max-file-size="10240"
/&gt;</pre>
        </div>
        
        <div class="alert alert-info" style="margin-top: 1rem;">
            <strong>Tip:</strong> The <code>max-file-size</code> is in kilobytes (KB). 
            10240 KB = 10 MB
        </div>
    </div>

    <h2>Example 5: Multiple Upload Zones</h2>
    <div class="example">
        <h3>Different File Types in Different Zones</h3>
        <p class="description">
            Create separate upload zones for different types of content with different settings.
        </p>
        
        <div class="grid">
            <div>
                <h4>📄 Documents</h4>
                <livewire:media::direct-upload 
                    disk="public"
                    collection="documents"
                    :max-file-size="51200"
                />
            </div>
            
            <div>
                <h4>🖼️ Images</h4>
                <livewire:media::direct-upload 
                    disk="public"
                    collection="images"
                    :max-file-size="10240"
                />
            </div>
        </div>
        
        <div class="code-block">
            <pre>&lt;div class="grid"&gt;
    &lt;div&gt;
        &lt;h4&gt;📄 Documents&lt;/h4&gt;
        &lt;livewire:media::direct-upload 
            disk="public"
            collection="documents"
            :max-file-size="51200"
        /&gt;
    &lt;/div&gt;
    
    &lt;div&gt;
        &lt;h4&gt;🖼️ Images&lt;/h4&gt;
        &lt;livewire:media::direct-upload 
            disk="public"
            collection="images"
            :max-file-size="10240"
        /&gt;
    &lt;/div&gt;
&lt;/div&gt;</pre>
        </div>
    </div>

    <h2>Example 6: With Event Listeners</h2>
    <div class="example">
        <h3>Custom Event Handling</h3>
        <p class="description">
            Listen to upload events and perform custom actions when files are uploaded or removed.
        </p>
        
        <div id="event-demo">
            <livewire:media::direct-upload 
                disk="public"
                collection="event-demo"
            />
            
            <div id="event-log" style="margin-top: 1rem; padding: 1rem; background: #f7fafc; border-radius: 6px; min-height: 100px;">
                <strong>Event Log:</strong>
                <div id="events" style="margin-top: 0.5rem; font-family: monospace; font-size: 0.875rem;"></div>
            </div>
        </div>
        
        <div class="code-block">
            <pre>&lt;livewire:media::direct-upload 
    disk="public"
    collection="event-demo"
/&gt;

&lt;script&gt;
    // Listen for file upload success
    window.addEventListener('fileUploaded', event =&gt; {
        console.log('File uploaded! Media ID:', event.detail);
        // Your custom code here
    });
    
    // Listen for file removal
    window.addEventListener('fileRemoved', event =&gt; {
        console.log('File removed! Media ID:', event.detail);
        // Your custom code here
    });
&lt;/script&gt;</pre>
        </div>
    </div>

    <h2>Configuration & Documentation</h2>
    <div class="grid">
        <div class="feature-card">
            <h4>📖 Documentation</h4>
            <p>Read the complete guide:</p>
            <ul>
                <li><a href="https://github.com/laravolt/laravolt/blob/master/packages/media/DIRECT_UPLOAD_GUIDE.md">DIRECT_UPLOAD_GUIDE.md</a></li>
                <li><a href="https://github.com/laravolt/laravolt/blob/master/packages/media/README.md">README.md</a></li>
            </ul>
        </div>
        
        <div class="feature-card">
            <h4>⚙️ Configuration</h4>
            <p>Publish config files:</p>
            <div class="code-block" style="font-size: 0.75rem;">
                <pre>php artisan vendor:publish --tag=laravolt-media-config</pre>
            </div>
            <p>Edit: <code>config/direct-upload.php</code></p>
        </div>
        
        <div class="feature-card">
            <h4>🧪 Testing</h4>
            <p>Run tests:</p>
            <div class="code-block" style="font-size: 0.75rem;">
                <pre>php artisan test tests/Feature/Media/DirectUploadTest.php</pre>
            </div>
        </div>
    </div>

    <div class="alert alert-success" style="margin-top: 2rem;">
        <strong>✨ Pro Tip:</strong> For very large files (&gt;100MB), consider using the 
        <a href="/media/chunked-upload-examples" style="color: #22543d; text-decoration: underline;">Chunked Upload</a> 
        feature instead, which provides better reliability and resume capabilities.
    </div>

    @livewireScripts
    <script>
        // Event listener demo
        let eventCount = 0;
        const eventsDiv = document.getElementById('events');
        
        function logEvent(message, color = '#4299e1') {
            eventCount++;
            const time = new Date().toLocaleTimeString();
            const entry = document.createElement('div');
            entry.style.color = color;
            entry.style.marginBottom = '0.25rem';
            entry.textContent = `[${time}] ${message}`;
            eventsDiv.insertBefore(entry, eventsDiv.firstChild);
            
            // Keep only last 10 events
            while (eventsDiv.children.length > 10) {
                eventsDiv.removeChild(eventsDiv.lastChild);
            }
        }
        
        window.addEventListener('fileUploaded', event => {
            logEvent(`✅ File uploaded (ID: ${event.detail})`, '#48bb78');
        });
        
        window.addEventListener('fileRemoved', event => {
            logEvent(`🗑️ File removed (ID: ${event.detail})`, '#e53e3e');
        });
        
        // Log when page loads
        logEvent('📝 Page loaded - Ready to upload!', '#718096');
    </script>
</body>
</html>
