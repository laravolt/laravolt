<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle="checkall"]').forEach(parent => {
      const selector = parent.getAttribute('data-selector');
      const parentInput = parent.querySelector('input[type="checkbox"]');
      const childContainers = Array.from(document.querySelectorAll(selector));

      const childInputs = childContainers
        .map(container => container.querySelector('input[type="checkbox"]'))
        .filter(Boolean);

      const recomputeParent = () => {
        const total = childInputs.length;
        const checked = childInputs.filter(i => i.checked).length;
        parentInput.indeterminate = checked > 0 && checked < total;
        parentInput.checked = checked === total && total > 0;
        const ids = childInputs.filter(i => i.checked).map(i => i.value);
        document.getElementById('{{ $id }}')?.dispatchEvent(new CustomEvent('suitable.checkall.change', { detail: ids }));
      };

      // Initialize
      recomputeParent();

      // Parent toggles children
      parentInput.addEventListener('change', () => {
        const targetState = parentInput.checked;
        childInputs.forEach(i => { i.checked = targetState; });
        recomputeParent();
      });

      // Children update parent
      childInputs.forEach(input => {
        input.addEventListener('change', recomputeParent);
      });
    });
  });
</script>
