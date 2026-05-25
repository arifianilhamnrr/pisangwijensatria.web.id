</main>

<script>
// Toggle sidebar untuk mobile
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const hamburger = document.querySelector('.hamburger');
  const body = document.body;

  sidebar.classList.toggle('open');
  body.classList.toggle('sidebar-open');

  // Hide hamburger saat sidebar open, show saat close
  if (sidebar.classList.contains('open')) {
    hamburger.style.display = 'none';
  } else {
    hamburger.style.display = 'block';
  }
}

// Tutup sidebar saat klik overlay
document.addEventListener('click', function(e) {
  const sidebar = document.getElementById('sidebar');
  const hamburger = document.querySelector('.hamburger');

  if (sidebar && sidebar.classList.contains('open')) {
    if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
      sidebar.classList.remove('open');
      document.body.classList.remove('sidebar-open');
      hamburger.style.display = 'block'; // Show hamburger saat sidebar close
    }
  }
});

// Tutup sidebar saat klik link navigasi di mobile
document.querySelectorAll('.sidebar .nav-a').forEach(link => {
  link.addEventListener('click', function() {
    if (window.innerWidth <= 768) {
      const sidebar = document.getElementById('sidebar');
      const hamburger = document.querySelector('.hamburger');

      sidebar.classList.remove('open');
      document.body.classList.remove('sidebar-open');
      hamburger.style.display = 'block'; // Show hamburger saat sidebar close
    }
  });
});
</script>

</body>
</html>