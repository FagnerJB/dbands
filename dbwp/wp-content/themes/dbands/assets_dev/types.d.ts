declare const Alpine: typeof import('alpinejs')

interface Window {
   Alpine: typeof Alpine
}

interface iDbands {
   mainUrl: string
   catBase: string
   tagBase: string
   apiBase: string
   maxPages: number
}

declare
{
   var dbands: iDbands
}
