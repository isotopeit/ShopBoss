(($)=>{
    $(document).ready(()=>{
        $(document).on('click', '#product_load', loadXl);
        $(document).on('click', '#product_upload', triggerData);
        // $(document).on('dblclick', '.imp-tr', showAlert);
    });
})(jQuery)

const ELEMENT_LOADING = `<div class="removable-tr text-center"><p class="display-4 text-muted"><i class="icon-spinner9 icon-2x spinner"></i> LOADING...</p></div>`;

const loadXl = (event) => {
    $(event.target).prop('disabled', true);
    $("#xl-table").addClass("d-none");
    $("#table-area").append(ELEMENT_LOADING);
    const fileReader = new FileReader();
    fileReader.readAsBinaryString(document.querySelector("#product_input").files[0]);
    fileReader.onload = (event) => {
        const data = event.target.result;
        const workbook = XLSX.read(data, { type: "binary" });
        workbook.SheetNames.forEach(sheet => {
            const rowObject = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheet]);
            tableOutput(rowObject);
        });
    }
    $(event.target).prop('disabled', false);
}

const tableOutput = rows => {

    const headerArr = ['category_code','category_name','product_name','product_code','product_quantity','product_cost','product_price','product_unit','product_stock_alert'];
    const thead     = headerArr.map(th => {
            return (`<th>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked>
                            <label class="form-check-label" for="defaultCheck1" data-v="${th}" contenteditable="">
                            ${th}
                            </label>
                        </div>
                    </th>`);
    }).join('');

    $("#xl-table thead").html(`<tr><th></th>${thead}</tr>`);
    const tbody = rows.map((row)=> {
        return (
            `<tr class="imp-tr">
                <td>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </td>
                ${Object.entries(row).map((td)=> {
                    if(headerArr.includes(_.snakeCase(td[0]))) {
                        return `<td data-label="${_.snakeCase(td[0])}">${td[1]}</td>`
                    }
                }).join('')}
            </tr>`
        )
    }).join('');
    
    $('.removable-tr').remove();
    if(tbody.length > 0) {
        document.querySelector("#xl-table tbody").innerHTML = tbody.trim();
    }
    $("#xl-table").removeClass("d-none");

}

const triggerData = async ()=>{
    if($('#xl-table tbody tr').length < 1)
    {
        alert('Load XL File First');
        return false;
    }
    $('#product_upload').html(`<i class="fa fa-sync fa-spin"></i> Processing ...`).prop('disabled', true);
    await new Promise(r => setTimeout(r, 1000));

    const theads = [];
    const trs    = $('#xl-table tbody tr');

    for (const input of $('#xl-table thead input')) {
        if ($(input).prop('checked')) {
            theads.push($('#xl-table thead th').index($(input).closest('th')))
        }
    }
    
    for (const [ key, tr ] of Object.entries(trs)) {
        const tds  = $(tr).find('td');
        if ($(tds[0]).find('input').is(':checked')) {
            const row = {};
            $.each(theads, (i, thIdx)=>{
                const labelKey  = $($('#xl-table thead th').get(thIdx)).find('label').text().trim();
                row[_.snakeCase(labelKey)] = $(tds.get(thIdx)).text();
            });
            $.ajax({
                url       : "/api/xl-product-create",
                method    : "post",
                dataType  : "json",
                data      : row,
                async     : false,
                success : function(res){
                    $(tr).removeClass('table-success').removeClass('table-danger').removeClass('table-warning');
                    $(tr).addClass(`table-success`).find('td').first().replaceWith(`
                        <td><b>${res.msg}</b></td>
                    `);
                },
                error   : function(err){
                    $(tr).removeClass('table-success').removeClass('table-danger').removeClass('table-warning');
                    $(tr).addClass(`table-danger`).find('td').first().replaceWith(`
                        <td><b>${err.responseJSON.msg ?? 'Something Went Wrong'}</b></td>
                    `);
                }
            });
        }
    }  
    $('#product_upload').html(`Upload`).prop('disabled', false);
}

function showAlert() {
    const msg = $(this).data('msg');
    console.log(msg);
    if(msg != undefined) pl_swal(msg)
}