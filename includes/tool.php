<div class="wrap">
    <h2><?php echo $title; ?></h2>
    <?php if( $export_file_url ): ?>
        <p>The export has successfully finished. <a href="<?php echo $export_file_url; ?>">Click here to downloaded a file with the exported data.</a> The link is valid until <?php echo $export_file_valid_until; ?>.</p>
    <?php endif; ?>
    <?php foreach ( $errors as $error ): ?><p style="color:red"><?php echo $error ?></p><?php endforeach; ?>
    <form method="post">
        <?php echo wp_nonce_field( $ns ); ?>
        <?php submit_button( $submit_button_text, 'primary' ); ?>
    </form>
</div>
