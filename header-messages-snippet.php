<?php
// **Avisos Rotativos Topo**
// Snippet para Code Snippets
// Shortcode: [avisos_rotativos_topo]

/**
 * 1) Registra menu de opções no Admin
 */
add_action('admin_menu', 'rotativo_add_admin_menu');
function rotativo_add_admin_menu() {
    add_menu_page(
        'Avisos Rotativos do Topo',
        'Avisos Rotativos',
        'manage_options',
        'rotativo-settings',
        'rotativo_options_page',
        'dashicons-megaphone',
        80
    );
}

/**
 * 2) Renderiza e salva as opções
 */
function rotativo_options_page() {
    if ( isset($_POST['rotativo_submit']) && check_admin_referer('rotativo_save_settings','rotativo_nonce') ) {
        $effects = ['fade','slide','zoom','typewriter','newsTicker'];
        $settings = [
            'quantity'    => intval($_POST['quantity']),
            'text_color'  => sanitize_text_field($_POST['text_color']),
            'bg_color'    => sanitize_text_field($_POST['bg_color']),
            'duration'    => max(100, intval($_POST['duration'])),
            'gap'         => max(0, intval($_POST['gap'])),
            'effect'      => in_array($_POST['effect'],$effects) ? $_POST['effect'] : 'fade',
            'font_size'   => intval($_POST['font_size']),
            'font_weight' => in_array($_POST['font_weight'],['normal','bold']) ? $_POST['font_weight'] : 'normal',
            'font_style'  => in_array($_POST['font_style'],['normal','italic','oblique']) ? $_POST['font_style'] : 'normal',
        ];
        $messages = [];
        if ( ! empty($_POST['messages']) && is_array($_POST['messages']) ) {
            foreach ($_POST['messages'] as $m) {
                $order = intval($m['order']);
                $text  = sanitize_text_field($m['text']);
                $link  = sanitize_text_field($m['link']);
                if ( $text !== '' ) {
                    $messages[] = ['order'=>$order,'text'=>$text,'link'=>$link];
                }
            }
        }
        usort($messages, function($a,$b){ return $a['order'] - $b['order']; });
        $settings['messages'] = $messages;
        update_option('rotativo_settings', $settings);
        echo '<div class="updated"><p>Configurações salvas.</p></div>';
    }

    $s   = get_option('rotativo_settings', []);
    $q   = $s['quantity']    ?? 3;
    $tc  = $s['text_color']  ?? '#000000';
    $bg  = $s['bg_color']    ?? '#ffffff';
    $dur = $s['duration']    ?? 1000;
    $gap = $s['gap']         ?? 200;
    $eff = $s['effect']      ?? 'fade';
    $fs  = $s['font_size']   ?? 16;
    $fw  = $s['font_weight'] ?? 'normal';
    $fst = $s['font_style']  ?? 'normal';
    $msgs= $s['messages']    ?? [];
    ?>
    <div class="wrap">
      <h1>Avisos Rotativos do Topo</h1>
      <form method="post"><?php wp_nonce_field('rotativo_save_settings','rotativo_nonce'); ?>
        <table class="form-table">
          <tr><th>Quantidade</th><td><input type="number" name="quantity" value="<?php echo esc_attr($q); ?>" min="1" /></td></tr>
          <tr><th>Cor do texto</th><td><input class="wp-color-picker-field" type="text" name="text_color" value="<?php echo esc_attr($tc); ?>" /></td></tr>
          <tr><th>Cor de fundo</th><td><input class="wp-color-picker-field" type="text" name="bg_color" value="<?php echo esc_attr($bg); ?>" /></td></tr>
          <tr><th>Duração (ms)</th><td><input type="number" name="duration" value="<?php echo esc_attr($dur); ?>" min="100" /></td></tr>
          <tr><th>Gap (ms)</th><td><input type="number" name="gap" value="<?php echo esc_attr($gap); ?>" min="0" /></td></tr>
          <tr><th>Efeito</th><td><select name="effect">
            <?php foreach(['fade'=>'Fade','slide'=>'Slide','zoom'=>'Zoom','typewriter'=>'Typewriter','newsTicker'=>'News Ticker Vertical'] as $k => $label) { ?>
              <option value="<?php echo $k; ?>" <?php selected($eff, $k); ?>><?php echo $label; ?></option>
            <?php } ?>
          </select></td></tr>
          <tr><th>Tamanho fonte (px)</th><td><input type="number" name="font_size" value="<?php echo esc_attr($fs); ?>" min="8" /></td></tr>
          <tr><th>Peso fonte</th><td><select name="font_weight"><option value="normal" <?php selected($fw,'normal'); ?>>Normal</option><option value="bold" <?php selected($fw,'bold'); ?>>Bold</option></select></td></tr>
          <tr><th>Estilo fonte</th><td><select name="font_style"><option value="normal" <?php selected($fst,'normal'); ?>>Normal</option><option value="italic" <?php selected($fst,'italic'); ?>>Itálico</option><option value="oblique" <?php selected($fst,'oblique'); ?>>Oblíquo</option></select></td></tr>
        </table>
        <h2>Mensagens</h2>
        <?php for($i = 0; $i < $q; $i++) {
            $m = $msgs[$i] ?? ['order'=>$i,'text'=>'','link'=>'']; ?>
          <fieldset style="border:1px solid #ccc;padding:8px;margin:8px 0;"><legend>Item <?php echo $i+1; ?></legend>
            Ordem: <input type="number" name="messages[<?php echo $i; ?>][order]" value="<?php echo esc_attr($m['order']); ?>" style="width:4em;" /><br/>
            Texto: <input type="text" name="messages[<?php echo $i; ?>][text]" value="<?php echo esc_attr($m['text']); ?>" style="width:60%;" /><br/>
            Link: <input type="text" name="messages[<?php echo $i; ?>][link]" value="<?php echo esc_attr($m['link']); ?>" style="width:60%;" placeholder="/path ou URL" />
          </fieldset>
        <?php } ?>
        <p class="submit"><button type="submit" name="rotativo_submit" class="button button-primary">Salvar</button></p>
        <h2>Shortcode</h2><p><code>[avisos_rotativos_topo]</code></p>
      </form>
    </div>
    <?php
}

/**
 * 3) Enfileira o color picker
 */
add_action('admin_enqueue_scripts','rotativo_admin_scripts');
function rotativo_admin_scripts($hook) {
    if ($hook !== 'toplevel_page_rotativo-settings') return;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_add_inline_script('wp-color-picker', 'jQuery(function($){ $(".wp-color-picker-field").wpColorPicker(); });');
}

/**
 * 4) Frontend do shortcode
 */
add_shortcode('avisos_rotativos_topo','rotativo_frontend');
function rotativo_frontend(){
    $s = get_option('rotativo_settings');
    if ( empty($s['messages']) ) return '';
    $msgs = array_slice($s['messages'], 0, intval($s['quantity']));
    if ( empty($msgs) ) return '';
    $tc  = esc_attr($s['text_color']);
    $bg  = esc_attr($s['bg_color']);
    $dur = max(100, intval($s['duration']));
    $gap = max(0, intval($s['gap']));
    $eff = esc_attr($s['effect']);
    $fs  = intval($s['font_size']);
    $fw  = esc_attr($s['font_weight']);
    $fst = esc_attr($s['font_style']);

    ob_start();
    echo '<div id="rotativo_container" style="background:'.$bg.';overflow:hidden;padding:5px;">';
    foreach ($msgs as $m) {
        $text = esc_html($m['text']);
        $link = esc_url($m['link']);
        if (! preg_match('#^(https?://|/)#i', $link)) {
            $link = home_url('/'.ltrim($link, '/'));
        }
        echo '<div class="rotativo_msg" data-full-text="'.esc_attr($text).'" '
           .'style="display:none;color:'.$tc.';font-size:'.$fs.'px;'
           .'font-weight:'.$fw.';font-style:'.$fst.';padding:5px 0;">'
           .'<a href="'.$link.'" style="color:'.$tc.';text-decoration:none;">'.$text.'</a></div>';
    }
    echo '</div>';
    ?>
    <script>
    jQuery(function($){
        var msgs = $('#rotativo_container .rotativo_msg'),
            curr = 0,
            ttl  = msgs.length,
            dur  = <?php echo $dur; ?>,
            gap  = <?php echo $gap; ?>,
            eff  = '<?php echo $eff; ?>';

        // show first message with effect
        var first = msgs.eq(0);
        switch (eff) {
            case 'slide':
                first.hide().slideDown(dur);
                break;
            case 'zoom':
                first.css({opacity:0,transform:'scale(0.5)',transition:'none'})
                     .show()
                     .css({transition:'all '+dur+'ms'})
                     .css({opacity:1,transform:'scale(1)'});
                break;
            case 'typewriter':
                first.show();
                var linkTxt = first.find('a'),
                    full = first.data('full-text'),
                    len  = full.length,
                    iv   = Math.max(20, dur/len);
                linkTxt.text('');
                for (let i = 1; i <= len; i++) {
                    (function(i){ setTimeout(function(){ linkTxt.text(full.substr(0,i)); }, i*iv); })(i);
                }
                break;
            case 'newsTicker':
                msgs.show();
                var wrap = $('#rotativo_container'), h = first.outerHeight();
                wrap.scrollTop(h);
                wrap.animate({scrollTop:0}, dur);
                break;
            default: // fade
                first.hide().fadeIn(dur);
        }

        function nextMsg() {
            var nxt   = (curr + 1) % ttl,
                curEl = msgs.eq(curr),
                nxtEl = msgs.eq(nxt);

            switch (eff) {
                case 'slide':
                    curEl.slideUp(dur, function(){
                        setTimeout(function(){ nxtEl.slideDown(dur); curr = nxt; }, gap);
                    });
                    break;
                case 'zoom':
                    curEl.fadeOut(dur, function(){
                        setTimeout(function(){
                            nxtEl.css({opacity:0,transform:'scale(0.5)',transition:'none'})
                                .show()
                                .css({transition:'all '+dur+'ms'})
                                .css({opacity:1,transform:'scale(1)'});
                            curr = nxt;
                        }, gap);
                    });
                    break;
                case 'typewriter':
                    curEl.hide();
                    nxtEl.show();
                    var linkTxt = nxtEl.find('a'),
                        full = nxtEl.data('full-text'),
                        len  = full.length,
                        iv   = Math.max(20, dur/len);
                    linkTxt.text('');
                    for (let i = 1; i <= len; i++) {
                        (function(i){
                            setTimeout(function(){ linkTxt.text(full.substr(0,i)); }, gap + i*iv);
                        })(i);
                    }
                    curr = nxt;
                    break;
                case 'newsTicker':
                    var wrap = $('#rotativo_container'), h = curEl.outerHeight();
                    wrap.animate({scrollTop: h*(curr+1)}, dur);
                    curr = nxt;
                    break;
                default: // fade
                    curEl.fadeOut(dur, function(){
                        setTimeout(function(){ nxtEl.fadeIn(dur); curr = nxt; }, gap);
                    });
            }
        }

        setInterval(nextMsg, dur*2 + gap);
    });
    </script>
    <?php
    return ob_get_clean();
}
