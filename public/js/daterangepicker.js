const tipo = [
    {name:"Ayer,Yesterday", key:"ayer"},
    {name:"Los últimos 7 días,Last 7 Days", key:"siete"},
    {name:"Últimos 30 días,Last 30 Days", key:"treinta"},
    {name:"Este mes,This Month", key:"mes"},
    {name:"El mes pasado,Last Month", key:"ultimomes"}
];

const getLocaleDateRange =function (){
    if (lang === "es"){
        return {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "Desde",
            "toLabel": "A",
            "customRangeLabel": "Personalizado"
        };
    }
    return {};
}
const getRanges =function (){
    if (lang === "es"){
        return {
            'Hoy': getDate("hoy"),
            'Ayer': getDate("ayer"),
            'Los últimos 7 días': getDate("siete"),
            'Últimos 30 días': getDate("treinta"),
            'Este mes': getDate("mes"),
            'El mes pasado': getDate("ultimomes")
         }
    }
    return {
        'Today': getDate("hoy"),
        'Yesterday': getDate("ayer"),
        'Last 7 Days': getDate("siete"),
        'Last 30 Days': getDate("treinta"),
        'This Month': getDate("mes"),
        'Last Month': getDate("ultimomes")
    }
}
const getDate = function (type){
    data = [moment().subtract(1, 'year'), moment()];
    switch(type){
        case "hoy":
            data = [moment(), moment()]
            break;
        case "ayer":
            data = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
            break;
        case "siete":
            data = [moment().subtract(6, 'days'), moment()];
            break;
        case "treinta":
            data = [moment().subtract(29, 'days'), moment()];
            break;
        case "mes":
            data = [moment().startOf('month'), moment().endOf('month')];
            break;
        case "ultimomes":
            data = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
            break;
    }
    return data;
}

const getKey = function (name){
    for (let i = 0; i < tipo.length; i++) {
        const element = tipo[i];
        if (element.name.indexOf(name) !== -1){
            return element.key;
        }
    }
    return "hoy";
}

const initDateRange = function(idDate, fnChange, fnCancel){
    let antiguo = getDate(getStorage(idDate));
    let start = antiguo[0];
    let end = antiguo[1];

    $('#'+idDate).daterangepicker({
        buttonClasses: 'm-btn btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        startDate: start,
        endDate: end,
        ranges: getRanges(),
        locale: getLocaleDateRange()
    }, function(start, end, label) {
        setStorage(idDate, getKey(label));
        $('#'+idDate+' .form-control').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        
        if (typeof(fnChange) === "function"){
            fnChange(start, end, label);
        }
        
    });
    
    $('#'+idDate).on('cancel.daterangepicker', function(ev, picker) {
        let hoy = getDate("hoy");
        setStorage(idDate, "hoy");
        picker.setStartDate(hoy[0]);
        picker.setEndDate(hoy[1]);
        if (typeof(fnCancel) === "function"){
            fnCancel(hoy[0], hoy[1]);
        }
    });

    $('#'+idDate+' .form-control').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    
    return {start:start, end:end};
}
