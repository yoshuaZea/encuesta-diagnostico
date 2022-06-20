import Swal from 'sweetalert2'

// Variables
const regExpEmail = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i

// Listener a los inputs
export const listenersInputs = selector => {
    const inputs = document.querySelectorAll(`${selector} .required`)

    // Evento blur
    inputs.forEach(input => {
        input.addEventListener('blur', validarInput)
    })

    // Evento input
    inputs.forEach(input => {
        input.addEventListener('input', validarInput)
    })
}

// Listener a los inputs
export const removelistenersInputs = selector => {
    const inputs = document.querySelectorAll(`${selector} .required`)

    const alertas = ['is-invalid']

    // Evento blur
    inputs.forEach(input => {
        // Limpiar alertas
        input.classList.remove(...alertas)

        if(input.nextElementSibling !== null){
            input.nextElementSibling.remove()
            input.previousElementSibling.classList.remove('text-danger')
        }

        input.removeEventListener('blur', validarInput)
    })

    // Evento input
    inputs.forEach(input => {
        input.removeEventListener('input', validarInput)
    })
}

function validarInput(e){
    if(e.target.type == 'radio') return;
    const estado = [[], ['is-invalid']]
    const success = ['border-success']
    let clases


    if(e.target.value.length === 0 || e.target.selectedIndex === 0) {
        clases = estado[1]
    } else {
        clases = estado[0]
    }

    // Agregar/eliminar clases
    e.target.classList.remove(...estado[0], ...estado[1])
    e.target.classList.add(...clases)

    // inyectar dinamicamente el div con el error
    if (clases.includes('is-invalid')) {
        if(e.target.nextElementSibling === null) {
            // construir un mensaje de error
            const errorDiv = document.createElement('div')
            errorDiv.appendChild( document.createTextNode('Este campo es obligatorio') )
            errorDiv.className = 'd-block text-danger text-xs mt-2'
            // insertar el error
            e.target.parentElement.insertBefore(errorDiv, e.target.nextElementSibling)
            // clase al label
            e.target.previousElementSibling.classList.add('text-danger')
        }
    } else {
        // limpiar el mensaje de error si existe
        if(e.target.nextElementSibling !== null && !e.target.nextElementSibling.classList.contains('no-blur')) {
            e.target.nextElementSibling.remove()
            e.target.previousElementSibling.classList.remove('text-danger')

            setTimeout(() => {
                e.target.previousElementSibling.classList.remove('border-success')
            }, 3000);
        }
    }
}

// Validar form
export const validateForm = (array) => {
    // Clases
    const alert = ['d-block', 'text-danger', 'text-xs', 'mt-2']
    const noValido = ['is-invalid']

    //Variables para mensaje de error
    let div = document.createElement('div')
    div.setAttribute("id", "msg-error")
    div.classList.add(...alert)
    let error = document.querySelector('#msg-error')


    //Tipo de mensajes por defualt
    let Mensaje1 = 'Ingresa el valor solicitado.',
        Mensaje2 = 'Selecciona una opción.',
        Mensaje3 = 'Selecciona el archivo solicitado.'

    for (let elem of array) {
        //Si contiene la clase required
        if(elem.classList.contains('required') || elem.classList.contains('radio')){
            //Elimina el mensaje de error
            if (error !== null) error.remove()
            elem.classList.remove(...noValido)
            elem.parentElement.classList.remove(...noValido)
            if(elem.nextElementSibling) elem.nextElementSibling.classList.remove(...noValido)
            elem.previousElementSibling && elem.previousElementSibling.classList.remove('text-danger')

            if (elem.nodeName == "INPUT" && elem.type !== "file") {
                if(elem.value == ""){
                    if(document.querySelector(`[input="${elem.name}"]`)){
                        const trix = document.querySelector(`[input="${elem.name}"]`)
                        div.innerHTML = Mensaje1
                        trix.parentElement.appendChild(div)
                        trix.classList.add(...noValido)
                        trix.focus()
                        return false
                    } else {
                        div.innerHTML = Mensaje1
                        elem.parentElement.appendChild(div)
                        elem.classList.add(...noValido)
                        elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                        elem.focus()
                        return false
                    }
                } else if(elem.name == 'CURP' && elem.value.length < 18){
                    div.innerHTML = 'La CURP debe tener 18 caracteres'
                    elem.parentElement.appendChild(div)
                    elem.classList.add(...noValido)
                    elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                    elem.focus()
                    return false
                } else if(elem.name == 'CURP' && !isCURP(elem.value)){
                    div.innerHTML = 'La CURP no es válida'
                    elem.parentElement.appendChild(div)
                    elem.classList.add(...noValido)
                    elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                    elem.focus()
                    return false
                } else if(elem.name == 'RFC' && elem.value.length < 13){
                    div.innerHTML = 'EL RFC debe tener 13 caracteres'
                    elem.parentElement.appendChild(div)
                    elem.classList.add(...noValido)
                    elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                    elem.focus()
                    return false
                } else if(elem.name == 'RFC' && !isRFC(elem.value)){
                    div.innerHTML = 'EL RFC no es válido'
                    elem.parentElement.appendChild(div)
                    elem.classList.add(...noValido)
                    elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                    elem.focus()
                    return false
                }

            } else if(elem.type == 'radio'){
                // Obtener radios
                const radios = [...document.querySelectorAll(`[name="${elem.name}"]`)]

                // Verificar si está checkeado
                const checked = radios.some(radio => radio.checked)

                if(!checked){
                    div.innerHTML = Mensaje2
                    div.classList.add('mt-1')
                    elem.parentElement.appendChild(div)
                    elem.classList.add(...noValido)
                    elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                    elem.focus()
                    return false
                }

            } else if (elem.nodeName == "SELECT" && !elem.multiple && elem.selectedIndex === 0) {
                div.innerHTML = Mensaje2
                elem.parentElement.appendChild(div)
                elem.classList.add(...noValido)
                elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                elem.focus()
                return false
            } else if (elem.nodeName == "SELECT" && elem.multiple && elem.selectedOptions.length == 0) {
                div.innerHTML = 'Debes seleccionar al menos una opción'
                elem.nextElementSibling.parentElement.appendChild(div)
                elem.nextElementSibling.classList.add(...noValido)
                elem.nextElementSibling.classList.add('rounded')
                elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                elem.nextElementSibling.focus()
                return false
            } else if (elem.nodeName == "TEXTAREA" && elem.value == "") {
                div.innerHTML = Mensaje1
                elem.parentElement.appendChild(div)
                elem.classList.add(...noValido)
                elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                elem.focus()
                return false
            } else if (elem.type == "file" && elem.value == "" && elem.files.length == 0) {
                div.innerHTML = Mensaje3;
                elem.parentElement.appendChild(div);
                elem.classList.add(...noValido)
                elem.previousElementSibling && elem.previousElementSibling.classList.add('text-danger')
                elem.focus();
                return false;
            } else if (elem.type == "file" && elem.files.length > 0
                && in_Array(elem.files[0].type, ['application/octet-stream', 'application/x-zip-compressed', 'application/zip'])
                && archivosComprimidos.length === 0) {
                msj_warning('Para el archivo comprimido debes seleccionar que contiene de las opciones mostradas')
                return false
            }
        }
    }

    return true
}

// Reemplazadores
export const runReplacers = () => {
    const rscn = document.querySelectorAll('.rscn')
    const rsc = document.querySelectorAll('.rsc')
    const rscc = document.querySelectorAll('.rscc')
    const ond = document.querySelectorAll('.ond')
    const rscorder = document.querySelectorAll('.rscorder')

    if(rscn.length > 0){
        rscn.forEach(element => {
            element.addEventListener('input', e => {
               let data =replaceSpecialCharactersAndNumbers(e.target.value)
               e.target.value = data
               e.target.focus()
            })
        })
    }

    if(rsc.length > 0){
        rsc.forEach(element => {
            element.addEventListener('input', e => {
               let data =replaceSpecialCharacters(e.target.value)
               e.target.value = data
               e.target.focus()
            })
        })
    }

    if(rscc.length > 0){
        rscc.forEach(element => {
            element.addEventListener('input', e => {
                let data = replaceSpecialCharactersAndCharacters(e.target.value)
                e.target.value = data
                e.target.focus()
            })
        })
    }

    if(ond.length > 0){
        ond.forEach(element => {
            element.addEventListener('input', e => {
                let data = onlyDecimalNumbers(e.target.value)
                e.target.value = data
                e.target.focus()
            })
        })
    }

    if(rscorder.length > 0){
        rscorder.forEach(element => {
            element.addEventListener('input', e => {
                let data = replaceSpecialCharactersOrder(e.target.value)
                e.target.value = data
                e.target.focus()
            })
        })
    }
}

// Clase rsc
export const replaceSpecialCharacters = string => string.replace(/\$|\!|\¡|\"|\#|\$|\%|\&|\/|\(|\)|\=|\¿|\?|\\|\{|\}|\[|\]|\;|\:|\^|\*|\'|\<|\>|\||\°|\¬|\@/g, '')

// Clase rscn
export const replaceSpecialCharactersAndNumbers = string => string.replace(/\$|\!|\¡|\"|\#|\$|\%|\&|\/|\(|\)|\=|\¿|\?|\\|\{|\}|\[|\]|\;|\:|\-|\_|\^|\*|\'|\<|\>|\||\°|\¬|\@|[0-9]+/g, '')

// Clase rscc
export const replaceSpecialCharactersAndCharacters = string => string.replace(/\D/g, '')

// Clase ond
export const onlyDecimalNumbers = string => string.replace(/\$|\!|\¡|\"|\#|\$|\%|\&|\/|\(|\)|\=|\¿|\?|\\|\{|\}|\[|\]|\;|\:|\-|\_|\^|\*|\'|\<|\>|\||\°|\¬|\@|\+|[A-z]+/g, '')

// Clase rscorder
export const replaceSpecialCharactersOrder = string => string.replace(/\$|\!|\¡|\"|\#|\$|\%|\&|\/|\(|\)|\=|\¿|\?|\\|\{|\}|\[|\]|\;|\:|\_|\^|\*|\'|\<|\>|\||\°|\¬|\@|[A-z]+/g, '')

// Solo dinero
export const input_money = element => {
    const input = document.querySelector(element)
    input.addEventListener('input', (element) => {
        let datos = element.target.value.replace(/\D/g, "")
                                        .replace(/([0-9])([0-9]{2})$/, '$1.$2')
                                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ",");
        element.target.value = datos;
        element.target.focus();
    })
}

export const isEmail = email => regExpEmail.test(email)

//Función para validar una CURP
export const isCURP = (curp) => {
    const re = /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/
    let validado = curp.match(re);

    if(!validado)  //Coincide con el formato general?
    	return false

    //Validar que coincida el dígito verificador
    const digitoVerificador = (curp17) => {
        //Fuente https://consultas.curp.gob.mx/CurpSP/
        let diccionario  = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ",
              lngSuma      = 0.0,
              lngDigito    = 0.0;

        for(let i=0; i<17; i++)
            lngSuma = lngSuma + diccionario.indexOf(curp17.charAt(i)) * (18 - i)

        lngDigito = 10 - lngSuma % 10;

        if (lngDigito == 10) return 0

        return lngDigito
    }

    if(validado[2] != digitoVerificador(validado[1]))
        return false

    return true; //Validado
}

//Función para validar una RFC
export const isRFC = (rfc, aceptarGenerico = true) => {
    const re       = /^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
    let   validado = rfc.match(re);

    if (!validado)  //Coincide con el formato general del regex?
        return false;

    //Separar el dígito verificador del resto del RFC
    const digitoVerificador = validado.pop(),
          rfcSinDigito      = validado.slice(1).join(''),
          len               = rfcSinDigito.length,

    //Obtener el digito esperado
          diccionario       = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ",
          indice            = len + 1;
    let   suma,
          digitoEsperado;

    if (len == 12) suma = 0
    else suma = 481; //Ajuste para persona moral

    for(let i = 0; i<len; i++)
        suma += diccionario.indexOf(rfcSinDigito.charAt(i)) * (indice - i);

    digitoEsperado = 11 - suma % 11;

    if (digitoEsperado == 11) digitoEsperado = 0;
    else if (digitoEsperado == 10) digitoEsperado = "A";

    //El dígito verificador coincide con el esperado?
    // o es un RFC Genérico (ventas a público general)?
    if ((digitoVerificador != digitoEsperado) && (!aceptarGenerico || rfcSinDigito + digitoVerificador != "XAXX010101000"))
        return false;
    else if (!aceptarGenerico && rfcSinDigito + digitoVerificador == "XEXX010101000")
        return false;
    return true;
}

export const msj_error = (msj) => {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: msj,
        confirmButtonColor: '#dc3545',
    });
}

export const msj_confirm = (title, msj) => {
    Swal.fire({
        icon: 'success',
        title: title,
        html: msj,
        confirmButtonColor: '#a5dc86',
    });
}

export const msj_success = (msj, time = 2500) => {
    Swal.fire({
        position: 'center',
        icon: 'success',
        text: msj,
        showConfirmButton: false,
        timer: time
    });
}

export const msj_warning = (text, title = '') => {
    Swal.fire({
        icon: 'warning',
        title: title,
        text: text,
        confirmButtonColor: '#ffc107',
    });
}

export const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

export const ucwords = (str) => {
    return str = str.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
        return letter.toUpperCase();
    });
};

export const getAntiguedad = (fecha) => {
    let hoy = new Date();
    hoy = hoy.getFullYear() + '-' + (hoy.getMonth() < 10 ? ("0" + (hoy.getMonth()+1)) : (hoy.getMonth() +1)) + '-' + (hoy.getDate() < 10 ? ("0"+hoy.getDate()) : hoy.getDate());
    let fechadesde = new Date(fecha).getTime();
    let fechahasta = new Date(hoy).getTime();
    let dias = fechahasta - fechadesde;
    let diff = dias / (1000 * 60 * 60 * 24);
    return diff;
}

export const ShowMessageLoading = () => {
    $(".text-loader").remove();
    let load = $("#contenedor_carga");
    load.append("<h5 class='text-loader' style='width: 200px; text-align: center'>Guardando datos por favor espera...</h5>");
    load.css("visibility","");
    load.css("opacity","");
    window.addEventListener('focus', HideMessageLoading, false);
}

export const HideMessageLoading = () => {
    window.removeEventListener('focus', HideMessageLoading, false);
    let contenedor = $("#contenedor_carga");
    contenedor.css("visibility","hidden");
    contenedor.css("opacity","0");
}

export const in_Array = (needle, haystack) => {
    let length = haystack.length;
    for(let i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

export const request = async () => {
    const url = `${ROUTE_APP}/actividades-asignadas-del-dia`

    const response = await $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        beforeSend: function(){},
        success: function(res){
            return res
        },
        complete: function(){},
        error: function(err){
            return err
        }
    })

    return response
}

export const html2txt = html => {
    return String(html)
        .replace(/&gt;/g, ">")
        .replace(/&lt;/g, "<")
        .replace(/&#039;/g, "'")
        .replace(/&quot;/g, '"')
        .replace(/&amp;/g, "&");
}

// Funcion de spinner global
export const showSpinner = () => {
    const spinnerLoader = document.querySelector(".page-loader")
    spinnerLoader.removeAttribute('hidden')
    spinnerLoader.style = {
        'visibility': '',
        'opacity': ''
    }
}

export const hideSpinner = () => {
    const spinnerLoader = document.querySelector(".page-loader")
    spinnerLoader.setAttribute('hidden', true)
    spinnerLoader.style = {
        'visibility': 'none',
        'opacity': '1'
    }
}

// Spinner
export const spinner = document.createElement('div')
spinner.classList.add('sk-chase-relative', 'sk-sm')
spinner.innerHTML = `<div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>
                    <div class="sk-chase-dot"></div>`

export const spinnerText = `<div class="spinner position-relative">
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                                <div class="sk-chase-dot"></div>
                            </div>`


// Ordernamiento de array object
Array.prototype.orderBy = function (field, type = 'asc') {
    if(type == 'asc'){
        this.sort((a,b) => {
            if(a[field] < b[field]) return -1
            if(a[field] > b[field]) return 1
            return 0
        })
    } else if(type == 'desc') {
        this.sort((a,b) => {
            if(a[field] > b[field]) return -1
            if(a[field] < b[field]) return 1
            return 0
        })
    }
}

// Formatear dinero MXN
export const formatearDinero = cantidad => {
    return Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2
            }).format(cantidad)
}

// Enviar mensajes whatsapp
export const sendWhatsappMessage = (num, text) => {
    window.open(`https://api.whatsapp.com/send?phone=52${num}&text=${text.trim()}`)
}
