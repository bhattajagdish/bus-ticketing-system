<!DOCTYPE html>
<html>
<head>
  <title>Modern Service Slider</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', sans-serif;
  overflow-x: hidden;
  background: #0f0f0f;
}

.slider-container {
  position: relative;
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transform: translateX(100%);
  transition: all 1s cubic-bezier(0.77, 0, 0.175, 1);
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  padding: 0 10%;
}

.slide::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 100%);
}

.slide.active {
  opacity: 1;
  transform: translateX(0);
  z-index: 1;
}

.slide.previous {
  transform: translateX(-100%);
  opacity: 0.5;
}

.content {
  position: relative;
  z-index: 2;
  max-width: 600px;
  color: #fff;
  transform: translateY(50px);
  opacity: 0;
  transition: all 0.8s ease 0.3s;
}

.slide.active .content {
  transform: translateY(0);
  opacity: 1;
}

.subtitle {
  font-size: 1.2rem;
  font-weight: 400;
  color: #00ff88;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 1rem;
}

.title {
  font-size: 3.5rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  text-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.btn {
  display: inline-flex;
  align-items: center;
  padding: 15px 35px;
  font-size: 1.1rem;
  color: #fff;
  background: linear-gradient(45deg, #00ff88 0%, #00b4d8 100%);
  border: none;
  border-radius: 30px;
  text-decoration: none;
  overflow: hidden;
  position: relative;
  transition: all 0.3s ease;
  box-shadow: 0 10px 20px rgba(0,255,136,0.2);
}

.btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(0,255,136,0.3);
}

.navigation {
  position: absolute;
  bottom: 50px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 15px;
  z-index: 3;
}

.dot {
  width: 15px;
  height: 15px;
  background: rgba(255,255,255,0.3);
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.dot.active {
  background: #00ff88;
  transform: scale(1.3);
}

.arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 60px;
  height: 60px;
  background: rgba(255,255,255,0.1);
  border: none;
  border-radius: 50%;
  backdrop-filter: blur(5px);
  cursor: pointer;
  z-index: 3;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.arrow:hover {
  background: rgba(0,255,136,0.2);
}

.arrow i {
  color: #fff;
  font-size: 1.5rem;
}

.arrow.left {
  left: 30px;
}

.arrow.right {
  right: 30px;
}

@media (max-width: 768px) {
  .slide {
    padding: 0 5%;
  }
  
  .title {
    font-size: 2.5rem;
  }
  
  .arrow {
    width: 50px;
    height: 50px;
  }
}

@media (max-width: 480px) {
  .title {
    font-size: 2rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
  
  .btn {
    padding: 12px 25px;
    font-size: 1rem;
  }
}
</style>
</head>
<body>

<div class="slider-container">
  <div class="slide" style="background-image: url('image/1.jpg');">
    <div class="content">
      <span class="subtitle">Premium Service</span>
      <h2 class="title">Experience Luxury Travel with Our Modern Fleet</h2>
      <a href="home.php" class="btn">Explore Now</a>
    </div>
  </div>

  <div class="slide" style="background-image: url('image/3.jpg');">
    <div class="content">
      <span class="subtitle">Easy Booking</span>
      <h2 class="title">Instant Online Reservations Anytime, Anywhere</h2>
      <a href="home.php" class="btn">Book Now</a>
    </div>
  </div>

  <div class="slide" style="background-image: url('image/6.jpg');">
    <div class="content">
      <span class="subtitle">Comfort First</span>
      <h2 class="title">Climate-Controlled Cabins for Perfect Journeys</h2>
      <a href="home.php" class="btn">View Options</a>
    </div>
  </div>

  <button class="arrow left"><i class="fas fa-chevron-left"></i></button>
  <button class="arrow right"><i class="fas fa-chevron-right"></i></button>
  
  <div class="navigation">
    <div class="dot active"></div>
    <div class="dot"></div>
    <div class="dot"></div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");
  let currentIndex = 0;
  let autoSlideInterval;

  function updateDots(index) {
    dots.forEach(dot => dot.classList.remove("active"));
    dots[index].classList.add("active");
  }

  function showSlide(index) {
    slides[currentIndex].classList.remove("active");
    slides[currentIndex].classList.add("previous");
    currentIndex = (index + slides.length) % slides.length;
    slides[currentIndex].classList.add("active");
    slides[currentIndex].classList.remove("previous");
    updateDots(currentIndex);
  }

  function nextSlide() {
    showSlide(currentIndex + 1);
  }

  function prevSlide() {
    showSlide(currentIndex - 1);
  }

  function startAutoSlide() {
    autoSlideInterval = setInterval(nextSlide, 5000);
  }

  // Event listeners
  document.querySelector(".arrow.left").addEventListener("click", prevSlide);
  document.querySelector(".arrow.right").addEventListener("click", nextSlide);
  
  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => showSlide(index));
  });

  // Initialize first slide
  slides[0].classList.add("active");
  startAutoSlide();

  // Pause on hover
  const container = document.querySelector(".slider-container");
  container.addEventListener("mouseenter", () => clearInterval(autoSlideInterval));
  container.addEventListener("mouseleave", startAutoSlide);
});
</script>
</body>
</html>