import { ElementWithXAttributes } from 'alpinejs'

type wpeActionType =
   | 'addClass'
   | 'after'
   | 'afterbegin'
   | 'afterend'
   | 'append'
   | 'before'
   | 'beforebegin'
   | 'beforeend'
   | 'cookie'
   | 'toast'
   | 'delay'
   | 'go'
   | 'hide'
   | 'html'
   | 'local'
   | 'open'
   | 'prepend'
   | 'reload'
   | 'remAttr'
   | 'remClass'
   | 'remove'
   | 'removeAttribute'
   | 'removeClass'
   | 'scroll'
   | 'scrollTo'
   | 'session'
   | 'setAttr'
   | 'setAttribute'
   | 'show'
   | 'text'
   | 'title'
   | 'trigger'
   | 'ignore'

/**
 * Object for the action handler.
 * @field {string} `action` the action. Required.
 * @field {string} `target` DOM element selector or key slug.
 * @field {string} `content` set content. Required in some actions.
 * @field {number} `duration` set duration or delay. Optional in some actions.
 */
interface wpeAction {
   action: wpeActionType
   target?: string
   content?: string | wpeAction[] | wpeAction
   duration?: number
}

interface wpeBody {
   [key: string]: string
}

interface $action {
   (
      action: wpeAction[] | wpeAction | wpeActionType,
      target?: string,
      content?: string,
      duration?: number
   ): void
}

interface $wp {
   ajax(action: string, body?: wpeBody): Promise<any>
   get(path: string, body?: wpeBody): Promise<any>
   post(path: string, body?: wpeBody): Promise<any>
   put(path: string, body?: wpeBody): Promise<any>
   patch(path: string, body?: wpeBody): Promise<any>
   delete(path: string, body?: wpeBody): Promise<any>
}

declare module 'alpinejs' {
   interface Alpine {
      $action: $action
      $wp: $wp
   }
   interface Magics<T> {
      $action: $action
      $wp: $wp
   }
}

document.addEventListener('alpine:init', () => {
   /**
    * An Alpine Magic shortcut for Clipboard API, that copies and pastes text.
    *
    * To copy a value to the clipboard, use:
    * ```js
    * $clipboard.copy(text)
    * ```
    *
    * To update an element atribute with the current clipboard value, use:
    * Example:
    * ```js
    * $clipboard.paste(element, attr = 'value')
    * ```
    *
    * @name $clipboard
    * @memberof Alpine
    */
})

export default function (Alpine) {
   Alpine.magic('copy', () => {
      if (location.protocol !== 'https:') {
         console.warn('Needs a https site.')
      }

      return (subject: string) => navigator.clipboard.writeText(subject)
   })

   Alpine.magic('action', (_el: ElementWithXAttributes) => {
      return (
         action: wpeAction[] | wpeAction | wpeActionType,
         target = '',
         content = '',
         duration = 5
      ) => {
         if ('string' === typeof action) {
            doAction({ action, target, content, duration })
            return
         }

         if (Array.isArray(action)) {
            action.forEach((act) => {
               doAction(act)
            })
         } else {
            doAction(action)
         }
      }
   })

   Alpine.magic('wp', (el: ElementWithXAttributes) => {
      return {
         ajax: (action: string, body: wpeBody = {}) => {
            if (objectIsEmpty(body)) {
               body = { action }
            } else {
               body.action = action
            }
            return doFetch(el, 'POST', body, 'ajax')
         },
         get: (path: string, body: wpeBody = {}) => {
            return doFetch(el, 'GET', body, path)
         },
         post: (path: string, body: wpeBody = {}) => {
            return doFetch(el, 'POST', body, path)
         },
         put: (path: string, body: wpeBody = {}) => {
            return doFetch(el, 'PUT', body, path)
         },
         patch: (path: string, body: wpeBody = {}) => {
            return doFetch(el, 'PATCH', body, path)
         },
         delete: (path: string, body: wpeBody = {}) => {
            return doFetch(el, 'DELETE', body, path)
         },
      }
   })
}

async function doFetch(
   el: ElementWithXAttributes | HTMLFormElement,
   method: 'GET' | 'POST' | 'PATCH' | 'PUT' | 'DELETE',
   body: wpeBody = {},
   path: string = 'ajax'
) {
   if (document.body.classList.contains('wpe-body-loading')) {
      return
   }

   document.body.classList.add('wpe-body-loading')
   el.classList.remove('wpe-el-loading')

   //@ts-expect-error
   let url = wpe.ajaxUrl
   if ('ajax' !== path) {
      //@ts-expect-error
      url = wpe.apiBase + path
   }

   const options: RequestInit = {
      method,
      mode: 'same-origin',
      referrerPolicy: 'same-origin',
      headers: {
         'Content-Type': 'application/json',
      },
   }

   if (objectIsEmpty(body) && el instanceof HTMLFormElement) {
      const formData = new FormData(el)
      formData.forEach(function (value, key) {
         if (typeof value === 'string') {
            body[key] = value
         }
      })
   }

   if (!objectIsEmpty(body)) {
      if ('GET' === method) {
         url += '?' + new URLSearchParams(body)
      } else {
         options.body = JSON.stringify(body)
      }
   }

   const res = await fetch(url, options)
   const actions = await res.json()

   const success = res.status < 400

   if (Array.isArray(actions)) {
      actions.forEach((action) => doAction(action, success))
   } else {
      doAction(actions, success)
   }
   document.body.classList.remove('wpe-body-loading')
   el.classList.remove('wpe-el-loading')

   return { success, data: actions }
}

const objectIsEmpty = (obj: any) => {
   return Object.keys(obj).length === 0 && obj.constructor === Object
}

const doAction = (act: wpeAction, success = false) => {
   if (!act.hasOwnProperty('action')) {
      return
   }

   if ('ignore' === act.action) {
      return
   }

   const content = act.content ?? ''
   /**
    * DOING DELAY FIRST TO AVOID TYPE ERRORS
    */
   if ('string' !== typeof content) {
      if ('delay' === act.action) {
         setTimeout(() => {
            if (Array.isArray(content)) {
               content.forEach((action) => doAction(action))
            } else {
               doAction(content)
            }
         }, (act.duration ?? 5) * 1000)
      }
      return
   }

   /**
    * DIRECT EFfECTS
    * Start:
    */
   switch (act.action) {
      case 'cookie':
      case 'session':
      case 'local':
         if (!success) {
            return
         }
         if (typeof act?.target === 'undefined') {
            throw new Error('target needed')
         }
         if (typeof act?.content === 'undefined') {
            throw new Error('content needed')
         }
         const key = act.target.replace(/[^a-zA-Z0-9_-]/g, '')
         const value = JSON.stringify(content)

      case 'session':
         if (act?.duration < 0) {
            sessionStorage.removeItem(`wpe_${key}`)
         } else {
            sessionStorage.setItem(`wpe_${key}`, value)
         }
         return

      case 'local':
         if (act?.duration < 0) {
            localStorage.removeItem(`wpe_${key}`)
         } else {
            localStorage.setItem(`wpe_${key}`, value)
         }
         return

      case 'cookie':
         document.cookie = `wpe_${key}=${value};Max-Age=${
            act.duration ?? 60 * 60 * 24 * 7
         };Path=/;SameSite=Lax;Secure`
         return

      case 'go':
      case 'open':
         const url = new URL(content).href

      case 'go':
         setTimeout(() => location.assign(url), (act.duration ?? 5) * 1000)
         return

      case 'open':
         setTimeout(() => window.open(url), (act.duration ?? 5) * 1000)
         return

      case 'reload':
         setTimeout(
            () => document.location.reload(),
            (act.duration ?? 5) * 1000
         )
         return

      case 'title':
         document.title = content
         return

      case 'toast':
         showToast(content, {
            duration: act.duration ?? 5,
         })
         return

      case 'scroll':
      case 'scrollTo':
         document
            .querySelector(act.target)
            .scrollIntoView({ behavior: 'smooth' })
         return

      default:
         break
   }
   /**
    * End.
    */

   /**
    * MULTIPLE TARGETs ACTIONS
    * Start:
    */
   const renameInsertHTML = {
      after: 'afterend',
      before: 'beforebegin',
      append: 'beforeend',
      prepend: 'afterbegin',
   }

   act.action = renameInsertHTML[act.action] ?? act.action

   const targets = document.querySelectorAll(act.target)
   targets.forEach((el) => {
      switch (act.action) {
         case 'beforebegin':
         case 'afterbegin':
         case 'afterend':
         case 'beforeend':
            el.insertAdjacentHTML(act.action, content)
            return

         case 'text':
            el.textContent = content
            return

         case 'html':
            el.innerHTML = content
            return

         case 'show':
            el.classList.remove('d-none', 'hidden')
            return

         case 'hide':
            el.classList.add('d-none', 'hidden')
            return

         case 'removeClass':
         case 'remClass':
            el.classList.remove(...content.split(' '))
            return

         case 'addClass':
            el.classList.add(...content.split(' '))
            return

         case 'setAttribute':
         case 'setAttr':
            const [key, value] = content.split('=')
            el.setAttribute(key, value ?? 'true')
            return

         case 'removeAttribute':
         case 'remAttr':
            el.removeAttribute(content)
            return

         case 'remove':
            el.remove()
            return

         case 'trigger':
            el.dispatchEvent(
               new Event(String(content), {
                  bubbles: false,
                  cancelable: true,
               })
            )
            return

         default:
            throw new Error('invalid action')
      }
      /**
       * End.
       */
   })
}

interface toastOptions {
   duration?: number
   classes?: string
   link?: string
}

const showToast = (message: string, options: toastOptions = {}) => {
   const { duration, classes, link } = options

   let list = document.getElementById('toast-list')
   if (!list) {
      list = document.createElement('ul')
      list.id = 'toast-list'
      document.body.appendChild(list)
   }

   const toast = document.createElement('li')
   toast.role = 'alert'

   let allClasses = [
      'toast',
      'toast-hidden',
      classes ?? 'toast-success',
      'toast-index-' + document.querySelectorAll('.toast').length,
   ]
   toast.className = allClasses.join(' ')

   const text = document.createTextNode(message)

   if (link) {
      const hyperlink = document.createElement('a')
      hyperlink.href = link
      hyperlink.target = '_blank'

      hyperlink.appendChild(text)
      toast.appendChild(hyperlink)
   } else {
      toast.appendChild(text)
   }

   list.appendChild(toast)

   setTimeout(() => {
      toast.classList.remove('toast-hidden')
   }, 1)

   setTimeout(() => {
      toast.classList.add('toast-hidden')
   }, (duration ?? 5) * 1000)

   setTimeout(() => {
      list.removeChild(toast)
      if (!document.querySelectorAll('.toast').length) {
         document.body.removeChild(list)
      }
   }, (duration ?? 5) * 1000 + 501)
}
