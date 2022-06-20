import * as Yup from 'yup'

const erRSCN = /^[aA-zZáéíóúÁÉÍÓÚ\s]+$/g
const form = document.querySelector('#form-survey')
const formQuestions = document.querySelector('#form-survey-questions')
const formSuggestions = document.querySelector('#form-survey-suggestions')
const formChoosen = document.querySelector('#form-survey-choosen')

document.addEventListener('DOMContentLoaded', () => {
    if(form){

        form.addEventListener('submit', validateSubmit)

        // Add events to inputs
        let elements = form.querySelectorAll('.yup')

        if(elements){
            Object.values(elements).forEach(element => {
                element.addEventListener('input', e => fillInput(e))
                element.addEventListener('change', () => validateInputs(element))
                element.addEventListener('focusout', () => validateInputs(element))
            })
        }

    }

    if(formQuestions){
        formQuestions.addEventListener('submit', validateSubmitQuestions)
    }

    if(formChoosen){
        formChoosen.addEventListener('submit', validateSubmitChoosen)
    }
})

// SChema yup validation
const validationSchema = Yup.object().shape({
    nombre: Yup.string()
                .required('El campo es obligatorio')
                .min(3, 'Debe tener al menos 3 caracteres')
                .matches(erRSCN, 'El campo contiene caracteres no válidos'),
    email: Yup.string()
                .required('El campo es obligatorio')
                .email('El email no es válido'),
    estudios: Yup.array().of(Yup.number().min(1))
})

// Object to store inputs
const inputsForm = {
    nombre: document.querySelector('[name="nombre"]')?.value,
    email: document.querySelector('[name="email"]')?.value,
    estudios: document.querySelector('[name="estudios"]')?.value,
    pregunta_1: document.querySelector('[name="pregunta_1"]')?.value,
    pregunta_2: document.querySelector('[name="pregunta_2"]')?.value,
    pregunta_3: document.querySelector('[name="pregunta_3"]')?.value,
    pregunta_4: document.querySelector('[name="pregunta_4"]')?.value,
    pregunta_5: document.querySelector('[name="pregunta_5"]')?.value,
    pregunta_6: document.querySelector('[name="pregunta_6"]')?.value,
    pregunta_7: document.querySelector('[name="pregunta_7"]')?.value,
    sugerencias: document.querySelector('[name="sugerencias"]')?.value,
    eleccion: document.querySelector('[name="eleccion"]')?.value,
}

// Object to stros errors from inputs
const errorValidation = { ...inputsForm }

const validateInputs = async field => {
    await validationSchema
        .validateAt(field.name, inputsForm)
        .then((response) => {
            errorValidation[field.name] = ''
        })
        .catch(error => {
            console.log(error)
            errorValidation[field.name] = error.message
        })

    addErrorOnScreen()
}

const validateSubmit = async e => {
    e.preventDefault()

    await validationSchema.validate(inputsForm, { abortEarly: false })
        .then(response => {

            form.querySelector('[type="submit"]').setAttribute('disabled', true)
            form.querySelector('[type="submit"]').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'

            form.submit()
        })
        .catch(err => {

            console.log(err.inner)
            err.inner.forEach(error => {
                errorValidation[error.path] = error.message
            })

            addErrorOnScreen()
        })
}

const addErrorOnScreen = () => {
    // Add errors at screen
    Object.entries(errorValidation).forEach(error => {

        let parent = document.querySelector(`[name="${error[0]}"]`)?.parentElement

        if(!parent) return

        // Delete previous error if exists from DOM
        if(parent.querySelector('#error-msg')){
            parent.lastElementChild.remove()
            parent.firstChild.classList.remove('text-primary')
            parent.firstChild.nextElementSibling.classList.remove('is-invalid', 'border-danger')
        }

        // If has error validation
        if(error[1].length > 0){
            // Create div error
            const divError = document.createElement('div')
            divError.className = 'text-primary text-sm mt-2 ml-1'
            divError.innerText = error[1]
            divError.id = 'error-msg'

            // Add classes
            parent.firstChild.classList.add('text-primary')
            parent.firstChild.nextElementSibling.classList.add('is-invalid', 'border-danger')

            // Add div error into the DOM
            parent.appendChild(divError)
        }
    })
}

const fillInput = element => inputsForm[element.target.name] = element.target.value

const validateSubmitQuestions = e => {
    e.preventDefault()

    const options = document.querySelectorAll('[name="respuesta"]')

    if(!options) return

    let result = Object.values(options).some(option => option.checked)

    if(!result){

        document.querySelector('#msg-error') && document.querySelector('#msg-error').remove()

        let divQuestions = formQuestions.querySelector('#questions')
        let div = document.createElement('div')
        div.id = 'msg-error'
        div.className = 'd-block text-danger text-xs mt-2 ml-2'
        div.innerText = 'Debes seleccionar una opción para poder continuar'

        divQuestions.firstElementChild.nextElementSibling.appendChild(div)
        return
    }

    // Ok
    // formQuestions.querySelector('[type="submit"]').setAttribute('disabled', true)
    // formQuestions.querySelector('[type="submit"]').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
    formQuestions.submit()
}

const validateSubmitChoosen = e => {
    e.preventDefault()

    const options = document.querySelectorAll('[name="eleccion"]')

    if(!options) return

    let result = Object.values(options).some(option => option.checked)

    if(!result){

        document.querySelector('#msg-error') && document.querySelector('#msg-error').remove()

        let divQuestions = formChoosen.querySelector('#choosens')
        let div = document.createElement('div')
        div.id = 'msg-error'
        div.className = 'd-block text-danger text-xs mt-2 ml-2'
        div.innerText = 'Debes seleccionar una opción para poder continuar'

        divQuestions.firstElementChild.appendChild(div)
        return
    }

    // Ok
    formChoosen.querySelector('[type="submit"]').setAttribute('disabled', true)
    formChoosen.querySelector('[type="submit"]').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
    formChoosen.submit()
}
