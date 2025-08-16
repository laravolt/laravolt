<?php

namespace Laravolt\Platform\Services;

/**
 * Class LaravoltBladeDirectives
 *
 * Provides custom Blade directives for including styles and scripts
 * required by the Laravolt Platform.
 *
 * @package Laravolt\Platform\Services
 */
class LaravoltBladeDirectives
{
    /**
     * Generate HTML for including required stylesheets and theme logic.
     *
     * @param mixed $expression The expression passed to the Blade directive (not used).
     * @return string HTML markup for styles and theme scripts.
     */
    public static function styles($expression)
    {
        $accent = config('laravolt.ui.colors.'.config('laravolt.ui.color'), '#3b82f6');
        return <<<HTML
          <!-- Font -->
          <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

          <!-- CSS HS -->
          <link rel="stylesheet" href="/laravolt/assets/css/main.min.css?v=3.0.1">
          <link rel="stylesheet" href="/laravolt/assets/css/filepond.min.css">

          <!-- Theme Check and Update -->
          <script>
            const html = document.querySelector('html');
            const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
            const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
            else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
            else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
            else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
          </script>

          <link rel="stylesheet" href="/laravolt/assets/vendor/apexcharts/dist/apexcharts.css">
          <style type="text/css">
            :root { --accent: {$accent}; --accent-foreground: #ffffff; }
            .btn-accent { background-color: var(--accent) !important; color: var(--accent-foreground) !important; }
            .btn-accent:hover, .btn-accent:focus { filter: brightness(0.95); }
            .btn-accent-soft { color: var(--accent) !important; background-color: color-mix(in srgb, var(--accent) 12%, transparent) !important; }
            .btn-accent-soft:hover, .btn-accent-soft:focus { background-color: color-mix(in srgb, var(--accent) 18%, transparent) !important; }
            .link-accent { color: var(--accent) !important; }
            .link-accent:hover, .link-accent:focus { text-decoration: underline; }
            input[type="checkbox"], input[type="radio"] { accent-color: var(--accent); }
            input:focus, select:focus, textarea:focus {
              border-color: var(--accent) !important;
              box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 20%, transparent) !important;
            }
            .apexcharts-tooltip.apexcharts-theme-light
            {
              background-color: transparent !important;
              border: none !important;
              box-shadow: none !important;
            }
          </style>
        HTML;
    }

    /**
     * Generate HTML for including required JavaScript files and chart initializations.
     *
     * @param mixed $expression The expression passed to the Blade directive (not used).
     * @return string HTML markup for scripts and chart initializations.
     */
    public static function scripts($expression)
    {
        // Use nowdoc to avoid PHP variable interpolation inside JS template literals like `${listInputName}`
        return <<<'HTML'
          <!-- Required plugins -->
          <script src="/laravolt/assets/vendor/preline/dist/index.js?v=3.0.1"></script>
          <!-- Clipboard -->
          <script src="/laravolt/assets/vendor/clipboard/dist/clipboard.min.js"></script>
          <script src="/laravolt/assets/js/hs-copy-clipboard-helper.js"></script>
          <script src="/laravolt/assets/js/filepond.min.js"></script>
          <!-- Apexcharts -->
          <script src="/laravolt/assets/vendor/lodash/lodash.min.js"></script>
          <script src="/laravolt/assets/vendor/apexcharts/dist/apexcharts.min.js"></script>
          <script src="/laravolt/assets/vendor/preline/dist/helper-apexcharts.js"></script>
          <!-- JS INITIALIZATIONS -->

          <script>
            document.addEventListener("DOMContentLoaded", () => {
              const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.content || "";

              document
                .querySelectorAll('input[type="file"].filepond')
                .forEach((input) => {
                  const token = input.dataset.token || csrfMeta;
                  const processUrl =
                    input.dataset.mediaUrl || input.dataset.process || "/upload";
                  const revertUrl = input.dataset.revert || processUrl; // fallback
                  const limit = parseInt(input.dataset.limit, 10) || null;
                  const maxSizeKB = parseInt(input.dataset.fileMaxSize, 10) || null; // assuming KB (e.g. 1024)
                  const listInputName = input.dataset.fileuploaderListinput;
                  const extensions = (input.dataset.extensions || "")
                    .split(",")
                    .map((e) => e.trim().toLowerCase())
                    .filter((e) => e); // Ensure hidden list input (stores uploaded IDs)
                  let listInputEl = listInputName
                    ? document.querySelector(`input[name="${listInputName}"]`)
                    : null;
                  if (!listInputEl && listInputName) {
                    listInputEl = document.createElement("input");
                    listInputEl.type = "hidden";
                    listInputEl.name = listInputName;
                    input.insertAdjacentElement("afterend", listInputEl);
                  }
                  function updateList(pond) {
                    if (!listInputEl) return;
                    const ids = pond
                      .getFiles()
                      .filter((f) => f.serverId)
                      .map((f) => f.serverId);
                    listInputEl.value = ids.join(",");
                  }
                  const pond = FilePond.create(input, {
                    credits: false, // TODO: make donation after releasing new version
                    allowMultiple: input.hasAttribute("multiple"),
                    maxFiles: limit || undefined,
                    maxFileSize: maxSizeKB
                      ? Math.round(maxSizeKB / 1024) + "MB"
                      : undefined,
                    beforeAddFile: (file) => {
                      if (!extensions.length) return true;
                      const name = file.filename.toLowerCase();
                      const ok = extensions.some((ext) => name.endsWith("." + ext));
                      if (!ok) {
                        alert("File type not allowed. Allowed: " + extensions.join(", "));
                      }
                      return ok;
                    },
                    server: {
                      process: {
                        url: processUrl,
                        method: "POST",
                        ondata: (formData) => {
                          formData.append("_token", token);
                          formData.append("_key", input.name);
                          formData.append("_action", "upload");
                          return formData;
                        },
                        onload: (response) => {
                          try {
                            const json = JSON.parse(response);
                            if (json.success && json.files && json.files[0]) {
                              return json.files[0].data?.id ?? json.files[0].file ?? null;
                            }
                          } catch (e) {
                            console.error("Upload parse error", e, response);
                          }
                          return null;
                        },
                        onerror: (resp) => {
                          try {
                            const json = JSON.parse(resp);
                            alert(json.message || "Upload failed");
                          } catch {
                            alert("Upload failed");
                          }
                          return resp;
                        },
                      },
                      // Custom revert to send the uploaded media id (serverId) back for deletion
                      revert: (serverId, load, error) => {
                        const formData = new FormData();
                        formData.append('_token', token);
                        formData.append('_action', 'delete');
                        formData.append('id', serverId);
                        fetch(revertUrl, {
                          method: 'POST',
                          body: formData,
                          headers: { 'X-CSRF-TOKEN': token }
                        })
                        .then(res => {
                          if(!res.ok) throw new Error('Delete failed');
                          return res.text();
                        })
                        .then(() => {
                          load();
                          updateList(pond);
                        })
                        .catch(e => {
                          alert(e.message);
                          error(e.message);
                        });
                      },
                      headers: { "X-CSRF-TOKEN": token },
                    },
                  });
                  pond.on("processfile", () => updateList(pond));
                  pond.on("removefile", () => updateList(pond));
                });
            });
          </script>
        HTML;
    }
}
