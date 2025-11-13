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

  public function __construct($settings) {
    $this->width = $settings["width"];
    $this->height = $settings["height"];
    $this->margin_l = $settings["margin_l"]; 
    $this->margin_r = $settings["margin_r"]; 
    $this->margin_t = $settings["margin_t"]; 
    $this->margin_b = $settings["margin_b"]; 
    $this->x_label = $settings["x_label"];
    $this->y_label = $settings["y_label"];
    $this->x_step = $settings["x_step"];
    $this->y_step = $settings["y_step"];
    $this->max_x = $settings["max_x"];
    $this->max_y = $settings["max_y"];
    $this->min_x = $settings["min_x"];
    $this->min_y = $settings["min_y"];
    $this->grid_color = array_key_exists("grid_color", $settings) ? $settings["grid_color"] : "#ccc";
    $this->marker_size = array();
    $this->marker_color = array();
    $this->marker_opacity = array();
    $this->marker_type = array();
    $this->x_values = array();
    $this->y_values = array();
  }

  public function add_series($x, $y, $marker_size = 5, $marker_color = "red", $marker_opacity = 1.0, $marker_type = 'o') {
    $this->x_values[] = $x;
    $this->y_values[] = $y;
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

    $width = $this->width;
    $height = $this->height;
    $margin_l = $this->margin_l; 
    $margin_r = $this->margin_r; 
    $margin_t = $this->margin_t; 
    $margin_b = $this->margin_b; 
    $x_axis_len = $width - $margin_l - $margin_r;
    $y_axis_len = $height - $margin_t - $margin_b;
    $tot_series = count($this->x_values);
  
    echo "<svg xmlns='http://www.w3.org/2000/svg' width='" . $width . "' height='" . $height . "'>\n\n";
    
    # Define markers
    echo "<defs>\n";
    for ($i=0; $i<$tot_series; $i++) {
      $this->define_marker($i);
    }
    echo "</defs>\n\n";

    # Draw grid
    $this->draw_grid();
   
    # Draw series    
    echo "<svg style='overflow: visible;' transform='translate(0, " . $height . ") scale(1, -1)' x='" . $margin_l . "' y='" . $margin_b . "' width='" . $x_axis_len . "' height='" . $y_axis_len . "'>\n";
    for ($i=0; $i<$tot_series; $i++) {
      $this->draw_series($i);
    }
    echo "</svg>\n\n";

    echo "</svg>\n";
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
    $width = $this->width;
    $height = $this->height;
    $margin_l = $this->margin_l; 
    $margin_r = $this->margin_r; 
    $margin_t = $this->margin_t; 
    $margin_b = $this->margin_b; 
    $x_axis_len = $width - $margin_l - $margin_r;
    $y_axis_len = $height - $margin_t - $margin_b;

    $grid_color = $this->grid_color;

    # Horizontal grid
    $max_x = $this->max_x;
    $min_x = $this->min_x;
    $x_step = $this->x_step;
    $x_label = $this->x_label;

    $x_cnt = $min_x;
    while($x_cnt <= $max_x) {
      $x_val = $margin_l + ($x_cnt - $min_x) / ($max_x - $min_x) * $x_axis_len;
      echo "<text x='" . $x_val . "' y='" . $height - $margin_b + 20 . "' text-anchor='middle'>" . $x_cnt . "</text>\n";
      echo "<line x1='" . $x_val . "' y1='" . $margin_t . "' x2='" . $x_val . "' y2='" . $height - $margin_b . "' stroke='" . $grid_color ."' />\n";
      $x_cnt += $x_step;
    }
    echo "<text x='" . $margin_l + $x_axis_len / 2 . "' y='" . $height - $margin_b + 40  . "' text-anchor='middle'>" . $x_label . "</text>\n\n";
    
    # Vertical grid
    $max_y = $this->max_y;
    $min_y = $this->min_y;
    $y_step = $this->y_step;
    $y_label = $this->y_label;

    $y_cnt = $min_y;
    while($y_cnt <= $max_y) {
      $y_val = $y_axis_len + $margin_t - ($y_cnt - $min_y) / ($max_y - $min_y) * $y_axis_len;
      echo "<text y='" . $y_val . "' x='" . $margin_l - 10 . "' text-anchor='end'>" . $y_cnt . "</text>\n";
      echo "<line x1='" . $margin_l . "' y1='" . $y_val . "' x2='" . $width - $margin_r . "' y2='" . $y_val . "' stroke='" . $grid_color ."' />\n";
      $y_cnt += $y_step;
    }
    echo "<text y='" . $margin_t + $y_axis_len / 2 . "' x='0' text-anchor='start' >" . $y_label . "</text>\n\n";
 
    # y axis
    echo "<line x1='" . $margin_l . "' y1='0' x2='" . $margin_l . "' y2='" . $height - $margin_b . "' stroke='#000' stroke-width='2px' />\n"; 
    
    # x axis
    echo "<line x1='" . $margin_l - 1 . "' y1='" . $height - $margin_b . "' x2='" . $width . "' y2='" . $height - $margin_b . "' stroke='#000' stroke-width='2px' />\n\n";
  }

  public function draw_series($cnt) {
    #if(count($this->x_values[$i]) == 0 or count($this->y_values[$i] == 0))
    #  return -1;
    #if(count($this->x_values[$i]) != count($this->y_values[$i] == 0))
    #  return -1;

    $x_values = $this->x_values[$cnt];
    $y_values = $this->y_values[$cnt];
    $max_x = $this->max_x;
    $max_y = $this->max_y;
    $min_x = $this->min_x;
    $min_y = $this->min_y;

    for($i=0; $i<count($x_values); $i++) {
      echo "<use x='" . ($x_values[$i] - $min_x) / ($max_x - $min_x) * 100 . "%' y='" . ($y_values[$i] - $min_y) / ($max_y - $min_y) * 100 . "%' xlink:href='#marker_" . $cnt . "'></use>\n";
    }
  }
}

?>
