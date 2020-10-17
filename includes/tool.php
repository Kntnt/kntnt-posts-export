<div class="wrap">
    <h2><?php echo $title; ?></h2>
    <form method="post">
        <?php echo wp_nonce_field( $ns ); ?>
        <?php foreach ( $errors as $error ): ?><p style="color:red"><?php echo $error ?></p><?php endforeach; ?>
        <?php submit_button( $submit_button_text, 'primary' ); ?>
        <?php if ( $export ): ?>
            <textarea id="export" name="export" rows="25" cols="80"><?php echo $export; ?></textarea>
        <?php endif; ?>
    </form>
</div>
