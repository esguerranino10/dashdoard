    </div><!-- /page -->
  </div><!-- /main -->
</div><!-- /app -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function updateClock() {
    const now = new Date();
    document.getElementById('clockTime').textContent =
      now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('clockDate').textContent =
      now.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
  }
  updateClock();
  setInterval(updateClock, 1000);

  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarBackdrop').classList.remove('show');
  }
  document.getElementById('menuToggle').onclick = () => {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarBackdrop').classList.toggle('show');
  };
  document.getElementById('sidebarBackdrop').onclick = closeSidebar;
</script>
</body>
</html>
