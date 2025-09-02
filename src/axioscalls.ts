import axios from 'axios'
import type { AxiosResponse, AxiosError } from 'axios'
export interface Objet {
    id: number
    nom: string
    idstatut: string
}
export interface ApiResponseBL {
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

export async function getBatimentsListe(server: string = '', page: string, jsonCriteres: string = '{}'): Promise<ApiResponseBL> {
    console.log(jsonCriteres)
    const urlacl: string = `${server}${page}`
    const params = new URLSearchParams([['jsoncriteres', jsonCriteres]])
    try {
        const response: AxiosResponse<Objet[]> = await axios.get(urlacl, { params })
        const respData: ApiResponseBL= {
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