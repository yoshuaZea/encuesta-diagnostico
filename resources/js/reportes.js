import { listenersInputs, validateForm } from './helpers'

const forms = document.querySelectorAll('#form_exportar')
const startDate = document.querySelector('#fecha_inicio')
const endDate = document.querySelector('#fecha_fin')

document.addEventListener('DOMContentLoaded', () => {
    // Listener inputs
    listenersInputs('#form_exportar')

    // Validación de formulario
    if(forms && forms.length > 0){
        forms.forEach(form => {
            form.addEventListener('submit', e => {
                const array = [...form.elements]

                if(!validateForm(array)){
                    e.preventDefault()
                } else {
                    ButtonShowMessageLoading(form)
                }
            })
        })
    }

    if(startDate && endDate){
        startDate.addEventListener('change', changeAttributes)
        endDate.addEventListener('change', changeAttributes)
    }
})


const changeAttributes = e => {
    if(e.target.name === startDate.name){
        endDate.setAttribute('min', e.target.value)

    } else if(e.target.name === endDate.name) {
        startDate.setAttribute('max', e.target.value)
    }
}

const ButtonShowMessageLoading = (form) => {
    let loading = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Descargando...';
    let btn = form.querySelector('#btnSubmit');
    btn.innerHTML = loading;
    btn.setAttribute('disabled', true);
    window.addEventListener('focus', ButtonHideMessageLoading, false);
}

const ButtonHideMessageLoading = () => {
    let statics = '<i class="fas fa-download"></i> Exportar';
    let btns = document.querySelectorAll('#btnSubmit');
    btns.forEach(btn => {
        btn.innerHTML = statics;
        btn.removeAttribute('disabled');
    })
    window.removeEventListener('focus', ButtonHideMessageLoading, false);
}
