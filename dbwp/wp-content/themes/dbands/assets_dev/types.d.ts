declare const Alpine: typeof import('alpinejs')

interface Window {
   Alpine: typeof Alpine
}

interface iDbands {
   mainUrl: string
   catBase: string
   tagBase: string
   apiBase: string
}

declare
{
   var dbands: iDbands
}
