<?php
/**
 * @category   OpenCart
 * @package    OCU OCU Nova Poshta
 * @copyright  Copyright (c) 2011 Eugene Lifescale (a.k.a. Shaman)
 * @modify     Upgrade up to OpenCart 2.0.x with NovaPoshta API v2.0 by Alex Tymchenko
 * @license    http://www.gnu.org/copyleft/gpl.html     GNU General Public License, Version 3
 */

 ?>


<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-api"><?php echo $entry_api_key; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="novaposhta_api_key" value="<?php echo $novaposhta_api_key; ?>" placeholder="<?php echo $entry_api_key; ?>" id="input-api" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sender-org"><?php echo $entry_sender_organization; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="novaposhta_sender_organization" value="<?php echo $novaposhta_sender_organization; ?>" placeholder="<?php echo $entry_sender_organization; ?>" id="input-sender-org" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sender-person"><?php echo $entry_sender_person; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="novaposhta_sender_person" value="<?php echo $novaposhta_sender_person; ?>" placeholder="<?php echo $entry_sender_person; ?>" id="input-sender-person" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sender-phone"><?php echo $entry_sender_phone; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="novaposhta_sender_phone" value="<?php echo $novaposhta_sender_phone; ?>" placeholder="<?php echo $entry_sender_phone; ?>" id="input-sender-phone" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-free-total"><span data-toggle="tooltip" title="<?php echo $help_free_total; ?>"><?php echo $entry_free_total; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="novaposhta_free_total" value="<?php echo $novaposhta_free_total; ?>" placeholder="<?php echo $entry_free_total; ?>" id="input-free-total" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sender-city"><?php echo $entry_sender_city; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="novaposhta_sender_city" value="<?php echo $novaposhta_sender_city; ?>" placeholder="<?php echo $entry_sender_city; ?>" id="input-sender-city" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sender-city-ref"><?php echo $entry_sender_city_ref; ?></label>
                        <div class="col-sm-10">
                            <div id="novaposhta_sender_city_ref">Loading...</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sender-warehouse"><?php echo $entry_sender_warehouse; ?></label>
                        <div class="col-sm-10">
                            <div id="novaposhta_sender_warehouse">Loading...</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-10">
                            <select name="novaposhta_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $novaposhta_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-send-order-status"><?php echo $entry_send_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="novaposhta_send_order_status" id="input-send-order-status" class="form-control">
                                <option value="0"><?php echo $text_select; ?></option>
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $novaposhta_send_order_status) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-weight-class"><?php echo $entry_weight_class; ?></label>
                        <div class="col-sm-10">
                            <select name="novaposhta_weight_class_id" id="input-weight-class" class="form-control">
                                <?php foreach ($weight_classes as $weight_class) { ?>
                                <?php if ($weight_class['weight_class_id'] == $novaposhta_weight_class_id) { ?>
                                <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="novaposhta_status" id="input-status" class="form-control">
                                <?php if ($novaposhta_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="novaposhta_sort_order" value="<?php echo $novaposhta_sort_order; ?>" size="1" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"><!--

  function getCities(novaposhta_sender_city) {
    $.ajax({
      url: 'index.php?route=shipping/novaposhta/getCities&token=<?php echo $token; ?>&filter=' + encodeURIComponent(novaposhta_sender_city),
      dataType: 'json',
      success: function (json) {

        html = '<select name="novaposhta_sender_city_ref" id="input-sender-city-ref" class="form-control">';

        html += '<option value=""><?php echo $text_select; ?></option>';
        for (i = 0; i < json.length; i++) {
          if (json[i]['city'] == '<?php echo $novaposhta_sender_city; ?>') {
            html += '<option selected="selected" value="' + json[i]['ref'] + '">' + json[i]['city'] + " ref:" + json[i]['ref'] + '</option>';
              getWarehouses(json[i]['ref']);
          } else {
            html += '<option value="' + json[i]['ref'] + '">' + json[i]['city'] + " ref:" + json[i]['ref'] + '</option>';
          }
        }
        html += '</select>';

        $('#novaposhta_sender_city_ref').html(html);
      }
    });
  }

  function getWarehouses(novaposhta_sender_city_ref) {
    $.ajax({
      url: 'index.php?route=shipping/novaposhta/getWarehouses&token=<?php echo $token; ?>&filter=' + encodeURIComponent(novaposhta_sender_city_ref),
      dataType: 'json',
      success: function (json) {

        html = '<select name="novaposhta_sender_warehouse" id="input-sender-warehouse" class="form-control">';

        html += '<option value=""><?php echo $text_select; ?></option>';
        for (i = 0; i < json.length; i++) {
          if (json[i]['warehouse'] == '<?php echo $novaposhta_sender_warehouse; ?>') {
            html += '<option selected="selected" value="' + json[i]['warehouse'] + '">' + json[i]['warehouse'] + '</option>';
          } else {
            html += '<option value="' + json[i]['warehouse'] + '">' + json[i]['warehouse'] + '</option>';
          }
        }
        html += '</select>';

        $('#novaposhta_sender_warehouse').html(html);
      }
    });
  }

  $(document).ready(function() {
      var novaposhta_sender_city = '<?php echo $novaposhta_sender_city; ?>';
      if (novaposhta_sender_city) {
          getCities(novaposhta_sender_city);
      }
    var novaposhta_sender_city_ref = '<?php echo $novaposhta_sender_city_ref; ?>';
    if(novaposhta_sender_city_ref) {
        getWarehouses(novaposhta_sender_city_ref);
    }
  });

  $('#novaposhta-sender-city-ref').change(function() {
    $('#novaposhta_sender_warehouse').html('Loading...');
    getWarehouses($('#novaposhta-sender-city-ref').val());
  });

    $('#input-sender-city').keyup(function(){
        $('#novaposhta_sender_city_ref').html('Loading...');
        getCities($('#input-sender-city').val());
    });

//--></script>

<?php echo $footer; ?>
