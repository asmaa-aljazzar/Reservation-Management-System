<?php require __DIR__ . '/includes/layout/header.php'; require __DIR__ . '/includes/layout/navbar.php'; ?>
<main id="main-content">
    <section class="home-hero" id="home" aria-labelledby="hero-title">
      <div class="container home-hero__grid">
        <div class="home-hero__copy" data-reveal>
          <p class="eyebrow"><span class="eyebrow__dot" aria-hidden="true"></span>A quieter place to get things done</p>
          <h1 id="hero-title">Find your space<br>to <span>focus.</span></h1>
          <p class="home-hero__intro">Browse library study rooms, find a time that fits your day, and send a reservation request in just a few steps.</p>
          <div class="home-hero__actions"><a class="button button--primary" href="pages/rooms.php">Browse rooms <span aria-hidden="true">→</span></a><a class="home-hero__text-link" href="#how-it-works">How it works</a></div>
          <p class="home-hero__note"><span class="home-hero__note-mark" aria-hidden="true"></span>A little space can make a big difference.</p>
        </div>
        <div class="hero-visual">
          <div class="hero-visual__sun" aria-hidden="true"></div>
          <div class="hero-visual__room" aria-hidden="true">
            <div class="hero-visual__window"><span></span><span></span><span></span><span></span></div>
            <div class="hero-visual__shelf"><i></i><i></i><i></i><i></i></div>
            <div class="hero-visual__desk"><span class="hero-visual__laptop"></span><span class="hero-visual__mug"></span><span class="hero-visual__plant" aria-hidden="true"></span></div>
            <div class="hero-visual__chair"></div>
          </div>
          <div class="hero-visual__caption card"><span class="hero-visual__label">A PLACE TO FOCUS</span><h2>A little room to think.</h2><p>Find a study space that feels right for you.</p></div>
        </div>
      </div>
      <div class="home-hero__decoration home-hero__decoration--one" aria-hidden="true"></div><div class="home-hero__decoration home-hero__decoration--two" aria-hidden="true"></div>
    </section>

    <section class="home-section container" id="rooms" aria-labelledby="rooms-title">
      <div class="section-heading home-section__heading" data-reveal><div><p class="eyebrow">A good place to begin</p><h2 id="rooms-title">Spaces for every kind of study</h2><p class="text-muted">Find a quiet corner for solo work or room to think together.</p></div><a class="home-section__all-link" href="pages/rooms.php">See all rooms <span aria-hidden="true">→</span></a></div>
      <div class="room-grid">
        <article class="room-card card" data-reveal style="--reveal-delay: 0ms"><div class="room-card__art room-card__art--quiet" aria-hidden="true"><span class="room-card__art-mark">01</span><span class="room-card__art-label">QUIET ZONE</span></div><div class="room-card__body"><div class="room-card__topline"><span class="room-card__tag">Quiet study</span><span class="room-card__capacity">1–4 people</span></div><h3>Willow Room</h3><p>A calm room for reading and focused work.</p><div class="room-card__bottom"><span class="availability"><span class="availability__dot"></span>3 slots available</span></div></div></article>
        <article class="room-card card" data-reveal style="--reveal-delay: 90ms"><div class="room-card__art room-card__art--group" aria-hidden="true"><span class="room-card__art-mark">02</span><span class="room-card__art-label">IDEAS WELCOME</span></div><div class="room-card__body"><div class="room-card__topline"><span class="room-card__tag">Group study</span><span class="room-card__capacity">2–8 people</span></div><h3>Oak Meeting Room</h3><p>Bring your group together for shared ideas.</p><div class="room-card__bottom"><span class="availability"><span class="availability__dot"></span>5 slots available</span></div></div></article>
        <article class="room-card card" data-reveal style="--reveal-delay: 180ms"><div class="room-card__art room-card__art--window" aria-hidden="true"><span class="room-card__art-mark">03</span><span class="room-card__art-label">LIGHT &amp; FOCUS</span></div><div class="room-card__body"><div class="room-card__topline"><span class="room-card__tag">Individual study</span><span class="room-card__capacity">1–2 people</span></div><h3>Sunroom</h3><p>A bright little space for a quiet hour with your work.</p><div class="room-card__bottom"><span class="availability"><span class="availability__dot"></span>2 slots available</span></div></div></article>
      </div>
      <p class="sample-data-note">Preview cards use sample content. See all rooms for current availability.</p>
    </section>

    <section class="how-section" id="how-it-works" aria-labelledby="how-title"><div class="container">
      <div class="section-heading how-section__heading" data-reveal><p class="eyebrow">Simple from the start</p><h2 id="how-title">A few steps to your study space</h2></div>
      <div class="steps-grid">
        <article class="step-card" data-reveal style="--reveal-delay: 0ms"><span class="step-card__number">01</span><h3>Choose your room</h3><p>Browse spaces by room, date, and group size.</p></article>
        <article class="step-card" data-reveal style="--reveal-delay: 90ms"><span class="step-card__number">02</span><h3>Send a request</h3><p>Pick a time and tell the library what you’re planning.</p></article>
        <article class="step-card" data-reveal style="--reveal-delay: 180ms"><span class="step-card__number">03</span><h3>Check your status</h3><p>Sign in to see whether your request is pending, confirmed, or declined.</p></article>
      </div>
    </div></section>

    <section class="closing-cta container" id="get-started" data-reveal aria-labelledby="closing-title"><div><p class="eyebrow">Make room for a good study session</p><h2 id="closing-title">Your next focused hour starts here.</h2></div><a class="button button--primary" href="pages/rooms.php">Explore rooms <span aria-hidden="true">→</span></a></section>
  </main>

  <?php require __DIR__ . '/includes/layout/footer.php'; ?>






