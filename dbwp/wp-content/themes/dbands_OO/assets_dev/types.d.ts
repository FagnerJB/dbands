declare const Alpine: typeof import('alpinejs')

interface Window {
   Alpine: typeof Alpine
}

interface iDbands {
   mainUrl: string
   maxPages: number
   catBase: string
   tagBase: string
   debug: string
}

declare
{
   var dbands: iDbands
}
