<script>
  function swapLayout(active, imgSrc = null, imgLabel = null) {
    const previewImg = document.getElementById('deptPreviewImg');
    const labelSpan = document.querySelector('.dept-img-label');
    
    if (active) {
      if (imgSrc) previewImg.src = imgSrc;
      if (imgLabel) labelSpan.textContent = imgLabel;
    } else {
      previewImg.src = "<?= asset('assets/logo.jpg') ?>";
      labelSpan.textContent = "ISCAG Departments";
    }
  }

  // Add smooth transition to dept layout grid columns
  const deptLayout = document.getElementById('deptLayout');
  if (deptLayout) {
    deptLayout.style.transition = 'grid-template-columns 280ms cubic-bezier(.4,0,.2,1)';
  }

  // Hero Reveal Animation on Scroll/Load
  function reveal() {
    var reveals = document.querySelectorAll(".reveal");
    var windowHeight = window.innerHeight;
    
    for (var i = 0; i < reveals.length; i++) {
      var elementTop = reveals[i].getBoundingClientRect().top;
      var elementVisible = 40;
      
      if (elementTop < windowHeight - elementVisible) {
        reveals[i].classList.add("active");
      }
    }
  }

  window.addEventListener("scroll", reveal);
  window.addEventListener("resize", reveal);
  window.addEventListener("DOMContentLoaded", reveal);
  
  setTimeout(reveal, 150);
  window.addEventListener("load", reveal);
  reveal();
</script>
