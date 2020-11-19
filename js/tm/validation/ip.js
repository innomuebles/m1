Validation.add('validate-ip', 'Please enter a valid IP (separated with dot and no leading zeros)', function(v) {
    return Validation.get('IsEmpty').test(v) || /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])$/.test(v)
})