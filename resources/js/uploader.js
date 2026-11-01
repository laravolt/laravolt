/*
 * Laravolt uploader: turns every <input type="file" class="uploader"> rendered
 * by form()->uploader() into a FilePond field (https://pqina.nl/filepond, MIT).
 * FilePond and its plugins are loaded on demand, only on pages that have an
 * uploader.
 *
 * Data attributes, set by Laravolt\SemanticForm\Elements\Uploader:
 *   data-limit          maximum number of files (1 = a single file)
 *   data-file-max-size  maximum size of each file, in MB
 *   data-extensions     allowed extensions, comma separated: "jpg,png"
 *   data-media-url      upload endpoint (media::store, handler=fileuploader).
 *                       Without it the files are posted with the form as
 *                       <name>[] instead, so the form must be multipart.
 *   data-token          CSRF token for the upload endpoint
 *   data-list-input     hidden input that receives the JSON list of file URLs
 *                       (read on the server by request()->media($name))
 *   data-files          files already attached: [{file, name, size, type}]
 *
 * Fields added later (a modal, a fetched fragment) are picked up
 * automatically; call window.LaravoltUploader.init(root) to do it by hand.
 *
 * Source: resources/js/uploader.js. scripts/build-assets.mjs copies it into
 * resources/assets.zip and fills in LOCALES.
 */
(function () {
    'use strict';

    if (window.LaravoltUploader) return;

    var SELECTOR = 'input[type=file].uploader';
    var VENDOR = '/laravolt/assets/vendor/';
    var SCRIPTS = [
        'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js',
        'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js',
        'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js',
        'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.min.js',
    ];
    var STYLES = [
        VENDOR + 'filepond/dist/filepond.min.css',
        VENDOR + 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css',
        VENDOR + 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.min.css',
        '/laravolt/assets/css/uploader.css',
    ];
    // FilePond locale files shipped in the zip; filled in by scripts/build-assets.mjs.
    var LOCALES = [];

    var currentScript = document.currentScript;
    var version = currentScript ? new URL(currentScript.src, location.href).searchParams.get('v') : null;
    var loading = null;

    function withVersion(url) {
        return version ? url + '?v=' + encodeURIComponent(version) : url;
    }

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var script = document.createElement('script');
            script.src = withVersion(src);
            script.onload = resolve;
            script.onerror = function () {
                reject(new Error('Cannot load ' + src));
            };
            document.head.appendChild(script);
        });
    }

    function loadStyle(href) {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = withVersion(href);
        document.head.appendChild(link);
    }

    // FilePond locale for <html lang>: "id" -> id-id, "pt-BR" -> pt-br.
    function localeFor(lang) {
        lang = (lang || '').toLowerCase().replace('_', '-');
        if (!lang || lang === 'en' || lang.indexOf('en-') === 0) return null;
        var base = lang.split('-')[0];
        var exact = LOCALES.filter(function (name) {
            return name.replace('_', '-') === lang;
        });
        if (exact.length) return exact[0];
        var same = LOCALES.filter(function (name) {
            return name.replace('_', '-') === base + '-' + base;
        });
        if (same.length) return same[0];
        var any = LOCALES.filter(function (name) {
            return name.split(/[-_]/)[0] === base;
        });
        return any.length ? any[0] : null;
    }

    function loadFilePond() {
        if (loading) return loading;

        STYLES.forEach(loadStyle);

        var core = window.FilePond ? Promise.resolve() : loadScript(VENDOR + 'filepond/dist/filepond.min.js');
        var locale = localeFor(document.documentElement.lang);

        loading = core
            .then(function () {
                return Promise.all(SCRIPTS.map(loadScript));
            })
            .then(function () {
                window.FilePond.registerPlugin(
                    window.FilePondPluginFileValidateType,
                    window.FilePondPluginFileValidateSize,
                    window.FilePondPluginImagePreview,
                    window.FilePondPluginFilePoster
                );

                if (!locale) return;

                return import(withVersion(VENDOR + 'filepond/locale/' + locale + '.js'))
                    .then(function (module) {
                        window.FilePond.setOptions(module.default);
                    })
                    .catch(function () {
                        // Keep the English labels.
                    });
            });

        return loading;
    }

    function parseFiles(json) {
        try {
            var files = JSON.parse(json || '[]');
            return Array.isArray(files) ? files : [];
        } catch (e) {
            return [];
        }
    }

    function extensionOf(name) {
        var match = /\.([^./\\]+)$/.exec(name || '');
        return match ? '.' + match[1].toLowerCase() : '';
    }

    function notify(message) {
        if (typeof window.Toastify === 'function') {
            window.Toastify({ text: message, duration: 4000, gravity: 'top', position: 'center' }).showToast();
        } else {
            window.alert(message);
        }
    }

    function post(url, fields) {
        var body = new FormData();
        Object.keys(fields).forEach(function (key) {
            body.append(key, fields[key]);
        });

        return fetch(url, {
            method: 'POST',
            body: body,
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
    }

    function create(input) {
        var FilePond = window.FilePond;
        var data = input.dataset;
        var name = input.getAttribute('name') || '';
        var limit = Math.max(1, parseInt(data.limit, 10) || 1);
        var maxSize = parseFloat(data.fileMaxSize) || 0;
        var mediaUrl = data.mediaUrl || '';
        var token = data.token || '';
        var accepted = (data.extensions || '')
            .split(',')
            .map(function (ext) {
                return ext.trim().toLowerCase().replace(/^\./, '');
            })
            .filter(Boolean)
            .map(function (ext) {
                return '.' + ext;
            });

        // Hidden input with the JSON list of attached file URLs, the format
        // request()->media($name) reads.
        var list = document.createElement('input');
        list.type = 'hidden';
        list.name = data.listInput || '_' + name.replace(/\[\]$/, '');
        input.parentNode.insertBefore(list, input);

        // Media ids of the files uploaded on this page, by URL, so that "undo"
        // can delete them again.
        var uploaded = {};

        var existing = parseFiles(data.files).map(function (file) {
            var type = file.type || '';
            return {
                source: file.file,
                options: {
                    type: 'local',
                    file: { name: file.name || file.file, size: Number(file.size) || 0, type: type },
                    metadata: type.indexOf('image/') === 0 ? { poster: file.file } : {},
                },
            };
        });

        list.value = JSON.stringify(existing.map(function (file) {
            return file.source;
        }));

        var pond = null;

        function sync() {
            if (!pond) return;
            var urls = pond
                .getFiles()
                .map(function (item) {
                    return item.serverId;
                })
                .filter(Boolean);
            list.value = JSON.stringify(urls);
        }

        // FilePond reads options from the element's attributes and lets them
        // win over the options passed in, so take the name off the element.
        input.removeAttribute('name');

        var options = {
            // With an upload endpoint, only the hidden list input is posted
            // with the form; FilePond's own fields stay nameless.
            name: mediaUrl ? '' : name.replace(/\[\]$/, '') + '[]',
            storeAsFile: !mediaUrl,
            allowMultiple: limit > 1,
            maxFiles: limit,
            maxFileSize: maxSize > 0 ? maxSize + 'MB' : null,
            acceptedFileTypes: accepted,
            fileValidateTypeDetectType: function (file, type) {
                var ext = extensionOf(file.name);
                return Promise.resolve(accepted.indexOf(ext) !== -1 ? ext : type);
            },
            labelFileProcessingError: function (error) {
                return (error && error.body) || FilePond.getOptions().labelFileProcessingError;
            },
            credits: false,
            files: existing,
            onaddfile: sync,
            onprocessfile: sync,
            onprocessfilerevert: sync,
            onremovefile: sync,
            onreorderfiles: sync,
            onupdatefiles: sync,
        };

        if (mediaUrl) {
            options.server = {
                process: function (fieldName, file, metadata, load, error, progress, abort) {
                    var body = new FormData();
                    body.append('_token', token);
                    body.append('_key', 'file');
                    body.append('_action', 'upload');
                    body.append('file', file, file.name);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', mediaUrl);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.upload.onprogress = function (event) {
                        progress(event.lengthComputable, event.loaded, event.total);
                    };
                    xhr.onload = function () {
                        var response = null;
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            // Not JSON: an error page.
                        }
                        var media = response && response.success && response.files && response.files[0];
                        if (xhr.status >= 200 && xhr.status < 300 && media && media.file) {
                            uploaded[media.file] = media.data ? media.data.id : undefined;
                            load(media.file);
                        } else {
                            error((response && response.message) || 'Upload failed (HTTP ' + xhr.status + ')');
                        }
                    };
                    xhr.onerror = function () {
                        error('Network error');
                    };
                    xhr.send(body);

                    return {
                        abort: function () {
                            xhr.abort();
                            abort();
                        },
                    };
                },
                revert: function (url, load, error) {
                    var id = uploaded[url];
                    if (id === undefined || id === null) return load();

                    post(mediaUrl, { _token: token, _action: 'delete', id: id })
                        .then(function (response) {
                            if (!response.ok) throw new Error('HTTP ' + response.status);
                            delete uploaded[url];
                            load();
                        })
                        .catch(function (e) {
                            error(e.message);
                        });
                },
            };
        }

        pond = FilePond.create(input, options);
        var form = list.form;

        if (form && !form.hasAttribute('data-uploader-guard')) {
            form.setAttribute('data-uploader-guard', '');
            // Do not post the form while a file is still uploading: its URL
            // would be missing from the list.
            form.addEventListener('submit', function (event) {
                var busy = Array.prototype.some.call(form.querySelectorAll('.filepond--root'), function (root) {
                    var instance = FilePond.find(root);
                    return (
                        instance &&
                        instance.getFiles().some(function (item) {
                            return (
                                item.status === FilePond.FileStatus.PROCESSING ||
                                item.status === FilePond.FileStatus.PROCESSING_QUEUED
                            );
                        })
                    );
                });
                if (busy) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    notify(document.documentElement.lang.indexOf('id') === 0
                        ? 'Tunggu sampai semua berkas selesai diunggah.'
                        : 'Please wait until every file has finished uploading.');
                }
            });
        }

        return pond;
    }

    function init(root) {
        var inputs = Array.prototype.filter.call((root || document).querySelectorAll(SELECTOR), function (input) {
            return !input.hasAttribute('data-uploader-ready');
        });
        if (!inputs.length) return Promise.resolve([]);

        inputs.forEach(function (input) {
            input.setAttribute('data-uploader-ready', '');
        });

        return loadFilePond().then(function () {
            if (!window.FilePond.supported()) return [];
            return inputs.filter(function (input) {
                return input.isConnected;
            }).map(create);
        });
    }

    function watch() {
        var scheduled = false;
        new MutationObserver(function (mutations) {
            if (scheduled) return;
            var added = mutations.some(function (mutation) {
                return Array.prototype.some.call(mutation.addedNodes, function (node) {
                    return node.nodeType === 1 && (node.matches(SELECTOR) || node.querySelector(SELECTOR));
                });
            });
            if (!added) return;
            scheduled = true;
            setTimeout(function () {
                scheduled = false;
                init(document).catch(report);
            }, 0);
        }).observe(document.body, { childList: true, subtree: true });
    }

    function report(e) {
        if (window.console) console.error('[laravolt uploader]', e);
    }

    window.LaravoltUploader = { init: init };

    function start() {
        init(document).catch(report);
        watch();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
