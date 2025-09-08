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
        const respData: ApiResponseOL= {
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
        const respData: ApiResponseOL= {
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

export async function getCommunesListe(server: string = '', page: string): Promise<ApiResponseOL> {
    const urlol: string = `${server}${page}`
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlol)
        const respData: ApiResponseOL= {
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