<style>
  /* Default footer styles */
  .footer-line1 {
    border: none;
    border-top: 1px solid #000;
    width: 90%;   /* Use less than 100% to allow for side margins */
    margin: 20px 0; /* Center the line */
  }

  .responsive-footer1 {
    background-color: #fff;
    padding: 10px 0;
    text-align: center;
  }

  .footer-content1 p {
    display: inline-block;
    margin: 0;
    transition: color 0.3s;
    color: #000;
  }

  /* Responsive for small screens */
  @media (max-width: 768px) {
    .footer-line1 {
      width: 37%;  /* Keep it consistent */
      margin: 5px 0; /* Center the line on mobile too */
    }

    .footer-content1 p {
      display: block;
      width: 40%; /* Let it take full width */
    }
  }
</style>

<footer class="responsive-footer1">
  <hr class="footer-line1">
  <div class="footer-content1">
    <p 
      onmouseover="this.style.color='#196f38'" 
      onmouseout="this.style.color=''"  
    >
      &copy; 2025 Online Election Voting System
    </p>
  </div>
</footer>