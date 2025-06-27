# Laravolt Asset Extraction

This document explains how Laravolt automatically extracts ZIP assets during installation and deployment.

## Overview

Laravolt includes ZIP files with assets that need to be extracted to specific locations:

1. `resources/icons.zip` → extracted to `resources/icons/`
2. `resources/assets.zip` → extracted to `public/laravolt/`

## Automatic Extraction

### During Package Installation

When you install Laravolt using Composer, the assets will be automatically extracted through:

1. **Composer Scripts**: The `post-autoload-dump` script runs automatically after `composer install` or `composer update`
2. **Service Provider**: The `UiServiceProvider` checks for missing assets during application boot (in non-local environments)

### Manual Extraction

You can manually extract assets using the Artisan command:

```bash
php artisan laravolt:extract-assets
```

### During Installation Process

The extraction also runs as part of the standard Laravolt installation:

```bash
php artisan laravolt:install
```

This command will:

1. Create symlinks (`laravolt:link`)
2. Extract assets (`laravolt:extract-assets`)
3. Publish skeleton files
4. Publish migrations
5. Publish other assets

## Deployment Considerations

### For Production Deployment

The assets will be automatically extracted during deployment if:

- The ZIP files exist in the package
- The target directories don't already contain files
- PHP ZipArchive extension is available

### For Docker/Containerized Deployments

Make sure to:

1. Include the `php-zip` extension in your container
2. Ensure proper file permissions for extraction
3. Consider extracting assets during the container build process

### Directory Structure After Extraction

```
resources/
├── icons/          # Extracted from icons.zip
│   ├── duotone/
│   ├── light/
│   ├── regular/
│   └── solid/
└── ...

public/
├── laravolt/       # Extracted from assets.zip OR symlinked
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
└── ...
```

## Troubleshooting

### Missing ZipArchive Extension

If you see warnings about ZipArchive, install the PHP zip extension:

```bash
# Ubuntu/Debian
sudo apt-get install php-zip

# CentOS/RHEL
sudo yum install php-zip

# Or via package manager in your environment
```

### Permission Issues

Ensure your web server has write permissions to:

- `resources/icons/`
- `public/laravolt/`

### Manual Extraction

If automatic extraction fails, you can manually extract the files:

```bash
# Extract icons
unzip vendor/laravolt/laravolt/resources/icons.zip -d resources/icons/

# Extract assets
unzip vendor/laravolt/laravolt/resources/assets.zip -d public/laravolt/
```

## Development

### Skipping Extraction in Local Development

In local development, you can skip extraction by using symlinks:

```bash
php artisan laravolt:link
```

This creates a symlink from `public/laravolt` to the vendor package directory.

### Force Re-extraction

To force re-extraction of assets:

1. Delete the target directories
2. Run the extraction command:

```bash
rm -rf resources/icons public/laravolt
php artisan laravolt:extract-assets
```
