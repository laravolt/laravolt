"""Compare two screenshot sets from capture.mjs and write a Markdown report.

Usage: python3 compare.py <base-dir> <head-dir> <out-dir>
Needs ImageMagick 6 (`convert`, `compare`, `identify`) on PATH.
"""

import json
import subprocess
import sys
from pathlib import Path

base, head, out = (Path(p) for p in sys.argv[1:4])
out.mkdir(parents=True, exist_ok=True)


def run(*args):
    return subprocess.run(list(args), capture_output=True, text=True)


def size(path):
    w, h = run("identify", "-format", "%w %h", str(path)).stdout.split()
    return int(w), int(h)


rows = []
for head_png in sorted(head.glob("*.png")):
    base_png = base / head_png.name
    name = head_png.stem
    if not base_png.exists():
        rows.append((name, "new", ""))
        continue
    bw, bh = size(base_png)
    hw, hh = size(head_png)
    w, h = max(bw, hw), max(bh, hh)
    # Pad both to the same canvas so pages that changed height still compare.
    pb, ph = out / f"_{name}.base.png", out / f"_{name}.head.png"
    for src, dst in ((base_png, pb), (head_png, ph)):
        run("convert", str(src), "-background", "white", "-extent", f"{w}x{h}", str(dst))
    r = run("compare", "-metric", "AE", "-fuzz", "2%", str(pb), str(ph), str(out / f"{name}.diff.png"))
    try:
        changed = int(float(r.stderr.split()[0]))
    except (ValueError, IndexError):
        changed = -1
    pct = 100.0 * changed / (w * h) if changed >= 0 else -1
    note = "" if (bw, bh) == (hw, hh) else f"size {bw}x{bh} -> {hw}x{hh}"
    if pct != 0:
        run("convert", str(pb), str(ph), str(out / f"{name}.diff.png"), "+append", str(out / f"{name}.side-by-side.png"))
    else:
        (out / f"{name}.diff.png").unlink(missing_ok=True)
    pb.unlink(missing_ok=True)
    ph.unlink(missing_ok=True)
    rows.append((name, f"{pct:.2f}%" if pct >= 0 else "error", note))

reports = {}
for side, d in (("base", base), ("head", head)):
    f = d / "report.json"
    reports[side] = json.loads(f.read_text()) if f.exists() else {"pages": {}}

lines = ["## Visual comparison (base vs head)", ""]
lines.append(f"Hover rules gated by `@media (hover: hover)`: base **{reports['base'].get('hoverGated')}**, head **{reports['head'].get('hoverGated')}**")
lines += ["", "| Screenshot | Pixels changed (fuzz 2%) | Note |", "|---|---|---|"]
for name, pct, note in rows:
    lines.append(f"| {name} | {pct} | {note} |")

lines += ["", "### Console / page errors", "", "| Page | Base | Head |", "|---|---|---|"]
for key in sorted(set(reports["base"]["pages"]) | set(reports["head"]["pages"])):
    b = reports["base"]["pages"].get(key, {})
    h = reports["head"]["pages"].get(key, {})
    if b.get("errors") or h.get("errors") or b.get("status") != h.get("status"):
        fmt = lambda p: f"HTTP {p.get('status')}<br>" + "<br>".join(e.replace("|", "\\|")[:160] for e in p.get("errors", []))
        lines.append(f"| {key} | {fmt(b)} | {fmt(h)} |")

(out / "summary.md").write_text("\n".join(lines) + "\n")
print("\n".join(lines))
