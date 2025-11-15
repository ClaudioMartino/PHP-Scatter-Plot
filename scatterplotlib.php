<?php
class ScatterPlot {
  private $width;
  private $height;
  private $margin_l;
  private $margin_r;
  private $margin_t;
  private $margin_b;
  private $x_label;
  private $y_label;
  private $x_step;
  private $y_step;
  private $title;
  private $max_x;
  private $max_y;
  private $min_x;
  private $min_y;
  private $grid_color;
  private $marker_size;
  private $marker_color;
  private $marker_opacity;
  private $marker_type;
  private $x_values;
  private $y_values;
  private $series_name;
  private $legend;

  public function __construct($settings = array()) {
    $this->width = array_key_exists("width", $settings) ? $settings["width"] : 800;
    $this->height = array_key_exists("height", $settings) ? $settings["height"] : 400;
    $this->margin_l = array_key_exists("margin_l", $settings) ? $settings["margin_l"] : NULL;
    $this->margin_r = array_key_exists("margin_r", $settings) ? $settings["margin_r"] : 25;
    $this->margin_t = array_key_exists("margin_t", $settings) ? $settings["margin_t"] : NULL;
    $this->margin_b = array_key_exists("margin_b", $settings) ? $settings["margin_b"] : NULL;
    $this->x_label = array_key_exists("x_label", $settings) ? $settings["x_label"] : NULL;
    $this->y_label = array_key_exists("y_label", $settings) ? $settings["y_label"] : NULL;
    $this->x_step = array_key_exists("x_step", $settings) ? $settings["x_step"] : NULL;
    $this->y_step = array_key_exists("y_step", $settings) ? $settings["y_step"] : NULL;
    $this->title = array_key_exists("title", $settings) ? $settings["title"] : NULL;
    $this->max_x = array_key_exists("max_x", $settings) ? $settings["max_x"] : NULL;
    $this->max_y = array_key_exists("max_y", $settings) ? $settings["max_y"] : NULL;
    $this->min_x = array_key_exists("min_x", $settings) ? $settings["min_x"] : NULL;
    $this->min_y = array_key_exists("min_y", $settings) ? $settings["min_y"] : NULL;
    $this->grid_color = array_key_exists("grid_color", $settings) ? $settings["grid_color"] : "#ccc";
    $this->legend = array_key_exists("legend", $settings) ? $settings["legend"] : NULL;

    $this->marker_size = array();
    $this->marker_color = array();
    $this->marker_opacity = array();
    $this->marker_type = array();
    $this->x_values = array();
    $this->y_values = array();
    $this->series_name = array();

    # Set default margins according to labels
    if(is_null($this->margin_l))
      $this->margin_l = is_null($this->y_label) ? 50 : 100;
    if(is_null($this->margin_t))
      $this->margin_t = is_null($this->title) ? 25 : 50;
    if(is_null($this->margin_b))
      $this->margin_b = is_null($this->x_label) ? 25 : 50;
  }

  public function add_series($x, $y, $series_name = "?", $marker_size = 5, $marker_color = "red", $marker_opacity = 1.0, $marker_type = 'o') {
    $this->x_values[] = $x;
    $this->y_values[] = $y;
    $this->series_name[] = $series_name;
    $this->marker_size[] = $marker_size;
    $this->marker_color[] = $marker_color;
    $this->marker_opacity[] = $marker_opacity;
    $this->marker_type[] = $marker_type;
  }

  public function draw() {
    #if(count($this->x_values) == 0 or count($this->y_values == 0))
    #  return -1;
    #if(count($this->x_values) != count($this->y_values == 0))
    #  return -1;

    $tot_series = count($this->x_values);

    if(is_null($this->legend)) {
      if($tot_series > 1)
        $this->legend = true;
      else
        $this->legend = false;
    }

    $x_axis_len = $this->width - $this->margin_l - $this->margin_r;
    $y_axis_len = $this->height - $this->margin_t - $this->margin_b;
 
    echo "<svg xmlns='http://www.w3.org/2000/svg' width='" . $this->width . "' height='" . $this->height . "'>\n\n";
    
    # Define markers
    echo "<defs>\n";
    for ($i=0; $i<$tot_series; $i++) {
      $this->define_marker($i);
    }
    echo "</defs>\n\n";

    # Draw grid
    $this->draw_grid();

    # Add title
    if(!is_null($this->title))
      echo "<text x='" . $this->margin_l + $x_axis_len / 2 . "' y='20' text-anchor='middle'>" . $this->title . "</text>\n\n";
   
    # Draw series    
    for ($i=0; $i<$tot_series; $i++) {
      $this->draw_series($i);
    }

    # Add legend
    if($this->legend)
      $this->add_legend();

    echo "\n</svg>\n";
  }

  public function define_marker($cnt) {
    $marker_size = $this->marker_size[$cnt];
    $marker_color = $this->marker_color[$cnt];
    $marker_opacity = $this->marker_opacity[$cnt];
    $marker_type = $this->marker_type[$cnt];
    echo "  <symbol>\n";
    echo "    <g id='marker_" . $cnt . "'>\n";
    if($marker_type == 's')
      echo "      <rect opacity='" . $marker_opacity . "' fill='" . $marker_color . "' y='" . -$marker_size / 2 . "' x='" . -$marker_size / 2 . "' height='" . $marker_size . "' width='" . $marker_size . "' />\n";
    else if($marker_type == 'o')
      echo "      <circle opacity='" . $marker_opacity . "' fill='" . $marker_color . "' r='" . $marker_size / 2 . "' />\n";
    echo "    </g>\n";
    echo "  </symbol>\n";
  }

  public function draw_grid() {
    # Set default min and max (+/- 5%)
    $tot_series = count($this->x_values);
    if(is_null($this->min_x) or is_null($this->max_x)) {
      $min_x = min($this->x_values[0]);
      $max_x = max($this->x_values[0]);
      for ($i=1; $i<$tot_series; $i++) {
        if (min($this->x_values[$i]) < $min_x)
          $min_x = min($this->x_values[$i]);
        if (max($this->x_values[$i]) > $max_x)
          $max_x = max($this->x_values[$i]);
      }
      $offset = 0.05 * ($max_x - $min_x);
      if(is_null($this->min_x))
        $this->min_x = $min_x - $offset;
      if(is_null($this->max_x))
        $this->max_x = $max_x + $offset;
    }
    if(is_null($this->min_y) or is_null($this->max_y)) {
      $min_y = min($this->y_values[0]);
      $max_y = max($this->y_values[0]);
      for ($i=1; $i<$tot_series; $i++) {
        if (min($this->y_values[$i]) < $min_y)
          $min_y = min($this->y_values[$i]);
        if (max($this->y_values[$i]) > $max_y)
          $max_y = max($this->y_values[$i]);
      }
      $offset = 0.05 * ($max_y - $min_y);
      if(is_null($this->min_y))
        $this->min_y = $min_y - $offset;
      if(is_null($this->max_y))
        $this->max_y = $max_y + $offset;
    }

    # Set default steps
    if(is_null($this->x_step))
      $this->x_step = ($this->max_x - $this->min_x) / 5;
    if(is_null($this->y_step))
      $this->y_step = ($this->max_y - $this->min_y) / 3;

    # Horizontal grid
    $x_axis_len = $this->width - $this->margin_l - $this->margin_r;
    $x_cnt = $this->min_x;
    while($x_cnt <= $this->max_x) {
      $x_val = $this->margin_l + ($x_cnt - $this->min_x) / ($this->max_x - $this->min_x) * $x_axis_len;
      echo "<text x='" . $x_val . "' y='" . $this->height - $this->margin_b + 20 . "' text-anchor='middle'>" . round($x_cnt, 2). "</text>\n";
      echo "<line x1='" . $x_val . "' y1='" . $this->margin_t . "' x2='" . $x_val . "' y2='" . $this->height - $this->margin_b . "' stroke='" . $this->grid_color ."' />\n";
      $x_cnt += $this->x_step;
    }
    if(!is_null($this->x_label))
      echo "<text x='" . $this->margin_l + $x_axis_len / 2 . "' y='" . $this->height - 10 . "' text-anchor='middle'>" . $this->x_label . "</text>\n\n";
    
    # Vertical grid
    $y_axis_len = $this->height - $this->margin_t - $this->margin_b;
    $y_cnt = $this->min_y;
    while($y_cnt <= $this->max_y) {
      $y_val = $y_axis_len + $this->margin_t - ($y_cnt - $this->min_y) / ($this->max_y - $this->min_y) * $y_axis_len;
      echo "<text y='" . $y_val . "' x='" . $this->margin_l - 10 . "' text-anchor='end'>" . round($y_cnt, 2) . "</text>\n";
      echo "<line x1='" . $this->margin_l . "' y1='" . $y_val . "' x2='" . $this->width - $this->margin_r . "' y2='" . $y_val . "' stroke='" . $this->grid_color ."' />\n";
      $y_cnt += $this->y_step;
    }
    if(!is_null($this->y_label))
      echo "<text y='" . $this->margin_t + $y_axis_len / 2 . "' x='0' text-anchor='start' >" . $this->y_label . "</text>\n\n";
 
    # Horizontal axis
    echo "<line x1='" . $this->margin_l - 1 . "' y1='" . $this->height - $this->margin_b . "' x2='" . $this->width - $this->margin_r . "' y2='" . $this->height - $this->margin_b . "' stroke='#000' stroke-width='2px' />\n\n";

    # Vertical axis
    echo "<line x1='" . $this->margin_l . "' y1='" . $this->margin_t . "' x2='" . $this->margin_l . "' y2='" . $this->height - $this->margin_b . "' stroke='#000' stroke-width='2px' />\n"; 
  }

  public function draw_series($cnt) {
    #if(count($this->x_values[$i]) == 0 or count($this->y_values[$i] == 0))
    #  return -1;
    #if(count($this->x_values[$i]) != count($this->y_values[$i] == 0))
    #  return -1;

    $x_axis_len = $this->width - $this->margin_l - $this->margin_r;
    $y_axis_len = $this->height - $this->margin_t - $this->margin_b;
    $x_values = $this->x_values[$cnt];
    $y_values = $this->y_values[$cnt];

    for($i=0; $i<count($x_values); $i++) {
      echo "<use x='" . $this->margin_l + ($x_values[$i] - $this->min_x) / ($this->max_x - $this->min_x) * $x_axis_len . "' y='" . $this->height - $this->margin_b - ($y_values[$i] - $this->min_y) / ($this->max_y - $this->min_y) * $y_axis_len . "' xlink:href='#marker_" . $cnt . "'></use>\n";
    }
  }

  public function add_legend() {
    $tot_series = count($this->x_values);
    for($i=0; $i<$tot_series; $i++) {
      echo "<text x='" . $this->margin_l + 20 + 10 . "' y='" . $this->height - $this->margin_b + 5 - 20 * $tot_series + 20 * $i . "'>" . $this->series_name[$i] . "</text>\n";
      echo "<use x='" . $this->margin_l + 20 . "' y='" . $this->height - $this->margin_b - 20 * $tot_series + 20 * $i . "' xlink:href='#marker_" . $i . "'></use>\n";
    }
  }
}

?>
