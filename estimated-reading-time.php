<?php
/**
 * Plugin Name: Estimated Reading Time
 * Plugin URI: 
 * Description: A simple customizable plugin that displays article's estimated reading time.
 * Version: 1.0
 * Author: Andrei Pavlinov
 * Author URI: https://github.com/Lokinorse
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
header('Content-Type: text/html; charset=utf-8');

		$rt_reading_time_options = get_option( 'rt_reading_time_options' );
		add_option( 'rt_reading_time_options', $default_settings );

////////CLIENT////////
		add_action( 'the_content', 'add_estimated_time' );
		function add_estimated_time ( $content ) {
			//convert HTML from $content to plain text
			$content_to_plain_text =  htmlspecialchars(trim(strip_tags($content))) . PHP_EOL;
			// adding all cyrillic letters for adequate str_word_count method work
			$number_of_words = str_word_count($content_to_plain_text, 0, '"АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя"');
			$wpm = 60/get_option('words_per_minute_speed');
			//calculating reading time
			$time = $number_of_words * $wpm/60;

			///russian language logic -_-
			if($time>1){
				$time = round($time);
			} else {
				$time = round($time, 1);
			}
			$prefix;
			$minuti = [2,3,4];
			$minutRange = range(5,20);
			$minuta = [1,21,31,41,51,61];
			$minut = array_diff($minutRange, $minuta);
		
			switch(true){
				case $time<1||in_array($time, $minuti):
				$prefix = ' минуты';
				break;
				case in_array($time, $minuta):
				$prefix = ' минута';
				break;
				case in_array($time, $minut):
				$prefix = ' минут';
				break;
			}
			$textSpan  = get_option('span_text');
			$averageTimeSpan = $textSpan . ' '. $time . $prefix . $content;
			echo $time;
			return $averageTimeSpan;
		};
	



////////ADMIN////////
function ert_register_settings() {
   add_option( 'span_text', 'Среднее время прочтения: ');
   add_option( 'words_per_minute_speed', '250');
   register_setting( 'options_group', 'span_text' );
   register_setting( 'options_group', 'words_per_minute_speed' );
}
add_action( 'admin_init', 'ert_register_settings' );



function etm_register_options_page() {
  add_options_page('Estimated Reading Time', ' Estimated Reading Time', 'manage_options', 'etm', 'etm_options_page');
}
add_action('admin_menu', 'etm_register_options_page');

?>
<?php 
function etm_options_page()
{
?>
  <div>
  <?php screen_icon(); ?>
  <h2>Estimated reading time</h2>
  <form method="post" action="options.php">
  <?php settings_fields( 'options_group' ); ?>

  <table>
  <tr>
  <th scope="row"><label for="span_text">Plugin text:</label></th>
  <td><input style="width:400px !important;" type="textarea" id="span_text" name="span_text" value="<?php echo get_option('span_text'); ?>" /></td>
	</tr>
  <tr>
   <th scope="row"><label for="words_per_minute_speed">Average reading speed:</label></th>
  <td><input type="number" id="words_per_minute_speed" name="words_per_minute_speed" value="<?php echo get_option('words_per_minute_speed'); ?>" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
} ?>
