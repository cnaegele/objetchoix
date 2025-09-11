import axios from 'axios'
import type { AxiosResponse, AxiosError } from 'axios'
export interface Objet {
    id: number
    nom: string
    idstatut: string
}
export interface ApiResponseOL {
    success: boolean
    message: string
    data?: Objet[]
}
export interface Adresse {
    idadresse: number
    rue: string
    numero: string
    coordeo: number
    coordsn: number
}
export interface ApiResponseAL {
    success: boolean
    message: string
    data?: Adresse[]
}
// Interface générique pour les réponses API
interface ApiResponse<T> {
    success: boolean
    message: string
    data?: T[]
}

export async function getBatimentsListe(server: string = '', page: string, jsonCriteres: string = '{}'): Promise<ApiResponseOL> {
    console.log(jsonCriteres)
    const urlol: string = `${server}${page}`
    const params = new URLSearchParams([['jsoncriteres', jsonCriteres]])
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol, { params })
        const respData: ApiResponseOL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        //console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

export async function getBatimentsListeParAdresse(server: string = '', page: string, jsonCriteres: string = '{}'): Promise<ApiResponseOL> {
    /**
     * jsonCritere :
     * idadresse : identifian de l'adresse
     * baannexe (optionnel def 1) : 1 retourne les éventuels bâtiments annexes / 0 uniquement le bâtiment avec l'adresse
     * typeretoursp (optionnel def '') : 'objet' la sp retourne les data selon l'interface Objet    
    */
    console.log(jsonCriteres)
    const urlol: string = `${server}${page}`
    const params = new URLSearchParams([['jsoncriteres', jsonCriteres]])
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol, { params })
        const respData: ApiResponseOL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        //console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

export async function getParcellesListeParAdresse(server: string = '', page: string, jsonCriteres: string = '{}'): Promise<ApiResponseOL> {
    /**
     * jsonCritere :
     * idadresse : identifian de l'adresse
     * bactif (optionnel def 1) : 1 uniquement parcelles "active" / 0 avec parcelle inactive (radiée)
     * bstandard (optionnel def 1) : 1 retour parcelles standard / 0 pas de parcelle standard
     * bddp (optionnel def 1) : 1 retour parcelles DDP / 0 pas de DDP
     * bppe (optionnel def 1) : 1 retour des parcelles PPE / 0 pas de PPE
     * bcopr (optionnel def 1) : 1 retour des parcelles Copropriété / 0 pas de Copropriété
     * typeretoursp (optionnel def '') : 'objet' la sp retourne les data selon l'interface Objet    
     */
    console.log(jsonCriteres)
    const urlol: string = `${server}${page}`
    const params = new URLSearchParams([['jsoncriteres', jsonCriteres]])
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol, { params })
        const respData: ApiResponseOL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        //console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

export async function getParcellesListe(server: string = '', page: string, jsonCriteres: string = '{}'): Promise<ApiResponseOL> {
    console.log(jsonCriteres)
    const urlol: string = `${server}${page}`
    const params = new URLSearchParams([['jsoncriteres', jsonCriteres]])
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol, { params })
        const respData: ApiResponseOL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        //console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

export async function getCommunesParcelleListe(server: string = '', page: string): Promise<ApiResponseOL> {
    const urlol: string = `${server}${page}`
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol)
        const respData: ApiResponseOL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        //console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

export async function getRuesListe(server: string = '', page: string, jsonCriteres: string = '{}'): Promise<ApiResponseOL> {
    console.log(jsonCriteres)
    const urlol: string = `${server}${page}`
    const params = new URLSearchParams([['jsoncriteres', jsonCriteres]])
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol, { params })
        const respData: ApiResponseOL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

export async function getCommunesRueListe(server: string = '', page: string): Promise<ApiResponseOL> {
    const urlol: string = `${server}${page}`
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol)
        const respData: ApiResponseOL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

export async function getAdressesListe(server: string = '', page: string, jsonCriteres: string = '{}'): Promise<ApiResponseAL> {
    console.log(jsonCriteres)
    const urlal: string = `${server}${page}`
    const params = new URLSearchParams([['jsoncriteres', jsonCriteres]])
    try {
        const response: AxiosResponse<Adresse[]> = await axios.get(urlal, { params })
        const respData: ApiResponseAL = {
            "success": true,
            "message": `ok`,
            "data": response.data
        }
        console.log(respData)
        return respData
    } catch (error) {
        return traiteAxiosError(error as AxiosError)
    }
}

function traiteAxiosError<T>(error: AxiosError): ApiResponse<T> {
    let msgErr: string = ''
    if (error.response) {
        msgErr = `${error.response.data}<br>${error.response.status}<br>${error.response.headers}`
    } else if (error.request.responseText) {
        msgErr = error.request.responseText
    } else {
        msgErr = error.message
    }
    const respData: ApiResponse<T> = {
        "success": false,
        "message": `ERREUR. ${msgErr}`,
    }
    return respData
}