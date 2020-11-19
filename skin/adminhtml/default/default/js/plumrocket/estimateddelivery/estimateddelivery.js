pjQuery_1_9(document).ready(function() {

    var daysInMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    var _setDays = function($month) {
        var $days = pjQuery_1_9($month).parent().find('.dateperiod-day');
        $days.find('option').show();
        switch(daysInMonth[$month.value - 1]) {
            case 29:
                $days.find('option[value=30]').hide();
                if($days.val() == 30) {
                    $days.val(1);
                }
                // no break
            case 30:
                $days.find('option[value=31]').hide();
                if($days.val() == 31) {
                    $days.val(1);
                }
                // no break
        }
    }

	var _rowDateType = function($row, dateType) {
		$row.find('.dateperiod-recurring-date').toggle(dateType == 'recurring_date');
    	$row.find('.dateperiod-single-day').toggle(dateType == 'single_day');
    	$row.find('.dateperiod-period').toggle(dateType == 'period');
    }

	/* Delivery Holidays */
	// Add row.
	var $deliveryHolidaysGrid = pjQuery_1_9('#row_estimateddelivery_delivery_holidays > td.value .grid .data tbody');
    var deliveryHolidaysTemplate = $deliveryHolidaysGrid.find('tr:first-child').eq(0).html();
    $deliveryHolidaysGrid.find('tr:first-child').remove();

    pjQuery_1_9('#row_estimateddelivery_delivery_holidays > td.value > .dateperiod-add').on('click', function() {
        var name = 'dateperiod-' + Date.now();
        var template = deliveryHolidaysTemplate.split('_TMPNAME_').join(name);
        $deliveryHolidaysGrid.append('<tr>'+ template +'</tr>');

        var $row = $deliveryHolidaysGrid.find('tr:last-child');
        var dateType = $row.find('.dateperiod-type select').val();
        _rowDateType($row, dateType);
        return false;
    });

    // Change date type.
    $deliveryHolidaysGrid.on('change', '.dateperiod-type select', function() {
    	var $row = pjQuery_1_9(this).parent().parent();
    	_rowDateType($row, this.value);
    })
    .find('.dateperiod-type select').each(function() {
    	var $row = pjQuery_1_9(this).parent().parent();
    	_rowDateType($row, this.value);
    });

    // Change month.
    $deliveryHolidaysGrid.on('change', '.dateperiod-recurring-date .dateperiod-month', function() {
        _setDays(this);
    })
    .find('.dateperiod-recurring-date .dateperiod-month').each(function() {
         _setDays(this);
    });

    // Remove row.
    $deliveryHolidaysGrid.on('click', '.dateperiod-remove', function() {
    	pjQuery_1_9(this).parent().parent().remove();
    });

    // Scope.
    pjQuery_1_9('#estimateddelivery_delivery_holidays_inherit:checked').click().click();

    /* Shipping Holidays */
	// Add row.
	var $shippingHolidaysGrid = pjQuery_1_9('#row_estimateddelivery_shipping_holidays > td.value .grid .data tbody');
    var shippingHolidaysTemplate = $shippingHolidaysGrid.find('tr:first-child').eq(0).html();
    $shippingHolidaysGrid.find('tr:first-child').remove();

    pjQuery_1_9('#row_estimateddelivery_shipping_holidays > td.value > .dateperiod-add').on('click', function() {
        var name = 'dateperiod-' + Date.now();
        var template = shippingHolidaysTemplate.split('_TMPNAME_').join(name);
        $shippingHolidaysGrid.append('<tr>'+ template +'</tr>');

        var $row = $shippingHolidaysGrid.find('tr:last-child');
        var dateType = $row.find('.dateperiod-type select').val();
        _rowDateType($row, dateType);
        return false;
    });

    // Change date type.
    $shippingHolidaysGrid.on('change', '.dateperiod-type select', function() {
    	var $row = pjQuery_1_9(this).parent().parent();
    	_rowDateType($row, this.value);
    })
    .find('.dateperiod-type select').each(function() {
    	var $row = pjQuery_1_9(this).parent().parent();
    	_rowDateType($row, this.value);
    });

    // Change month.
    $shippingHolidaysGrid.on('change', '.dateperiod-recurring-date .dateperiod-month', function() {
        _setDays(this);
    })
    .find('.dateperiod-recurring-date .dateperiod-month').each(function() {
         _setDays(this);
    });

    // Remove row.
    $shippingHolidaysGrid.on('click', '.dateperiod-remove', function() {
    	pjQuery_1_9(this).parent().parent().remove();
    });

    // Scope.
    pjQuery_1_9('#estimateddelivery_shipping_holidays_inherit:checked').click().click();

});