<?php get_header(); ?>
<section class="section">
    <div class="container">
        <?php if (have_posts()): ?>
        <div class="grid grid--3">
            <?php while (have_posts()): the_post(); ?>
            <article style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <a href="<?php the_permalink(); ?>" style="display: block; aspect-ratio: 4/3; overflow: hidden;">
                    <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                    <?php endif; ?>
                </a>
                <div style="padding: 20px;">
                    <h3 style="font-family: var(--font-display); font-size: 1.25rem;"><a href="<?php the_permalink(); ?>" style="text-decoration: none; color: var(--primary);"><?php the_title(); ?></a></h3>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
        <?php else: ?>
        <p>No hay contenido.</p>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>
