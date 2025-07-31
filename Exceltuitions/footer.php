  </main>

  <footer id="footer" class="footer">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename"><strong><span style="color:#042844">Excel</span><span style="color:#3db609">Tuitions.com</span></strong></span>
          </a>
          <div class="footer-contact pt-3">
            <p>A108 Adam Street</p>
            <p>New York, NY 535022</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
            <p><strong>Email:</strong> <span>info@exceltuitions.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Slot Booking</h4>
          <ul>
            <li><a href="#">For Student</a></li>
            <li><a href="#">For Faculty</a></li>  
            <li><a href="#">at Offline</a></li>  
            <li><a href="#">at Online</a></li>            
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Administration</h4>
          <ul>
            <li><a href="#">Cources/Subjects</a></li>
            <li><a href="#">Student Registration</a></li>
            <li><a href="#">Faculty Registration</a></li>
            
          </ul>
        </div>

         <div class="col-lg-2 col-md-3 footer-links">
          <h4>Download</h4>
          <ul>
            
            <li><a href="#">Faculty Login</a></li>
            <li><a href="#">Admissions</a></li>
            <li><a href="#">Quick Links</a></li>
          </ul>
        </div> 

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename"><strong><span style="color:#042844">Excel</span><span style="color:#3db609">Tuitions.com</span></strong></strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="#" >srisoultech.com</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
<script>
    // Scrolling title text
    let scrollTitle = " ExcelTuitions.com - The Best Online/Offline Tutorials | ";
    let titleIndex = 0;

    function scrollTitleText() {
        document.title = scrollTitle.substring(titleIndex) + scrollTitle.substring(0, titleIndex);
        titleIndex = (titleIndex + 1) % scrollTitle.length;
        setTimeout(scrollTitleText, 200); // Adjust speed (200 ms delay)
    }

    // Start the scrolling effect
    window.onload = scrollTitleText;
</script>
</body>

</html>