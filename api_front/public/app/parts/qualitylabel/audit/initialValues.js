// import { ValidationSchema } from './Context'

export const initialValues = {
    qualityLabel: {
        value: '',
        required: true,
    },
    since: {
        value: '',
        error: ''
    },
    until: {
        value: '',
        error: '',
        required: true,
        validate: "date",
        helperText: 'Cette date est requise'
    },
    extern: {
        value: false,
        error: '',
        helperText: "Est-ce une audit externe ?"
    },
    type: {
        value: null,
        error: '',
        helperText: "Est-ce un audit préparatoire ?"
    },
    previousAudit: {
        value: false,
        error: '',
        helperText: "Voulez-vous utilisé les paramétrages d'une audit précédente ?"
    },
    students: {
        value: [],
        error: '',
        validate: "array",
        minLength: 1,
        required: true,
        type: 'array',
        helperText: "Sélectionnez au moins un apprenants !"
    },
    formations: {
        value: [],
        error: '',
        validate: "array",
        minLength: 1,
        type: 'array'
    },
    groups: {
        value: [],
        error: '',
        validate: "array",
        minLength: 1,
        type: 'array'
    },
}
