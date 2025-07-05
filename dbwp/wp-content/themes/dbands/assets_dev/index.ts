import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import cav from '@ctrlaltvers/alpine'

import dbtv from './tv'

Alpine.plugin(persist)
Alpine.plugin(cav)

document.addEventListener('alpine:init', () => {
   Alpine.data('dbands', function () {
      return {
         showCookies: Alpine.$persist(true),
         tmdbItem: null,
         pagination: {
            archiveUrl: dbands.mainUrl,
            currentPage: 1,
            maxPage: 0,
            infinite: false,
            currentEl: null as Element,
         },
         search: {
            select: false,
            type: '',
            placeholder: '',
            selected: '',
         },
         gallery: {
            open: false,
            current: 0,
            images: [],
            lastX: null as number | null,
         },
         // @ts-expect-error
         tv: new dbtv(this),

         init() {
            if (!this.$is.bot()) {
               this.$do('remClass', 'body', 'no-js')
            }

            this.pagination.archiveUrl =
               this.$refs.archiveLink?.dataset.archiveLink ?? dbands.mainUrl

            this.$watch('search.type', (type) => this.updateSearch(type))
            this.$watch('tv.current', (current) =>
               this.tv.onCurrentChange(current)
            )
            this.$watch('tv.status', (status) => this.tv.onStatusChange(status))
            this.$watch('tv.state', (state) => this.tv.onStateChange(state))

            // NAVIGATION
            document.addEventListener(
               'click',
               (e: Event) => {
                  const el = (e.target as Element)?.closest(
                     `a[href^="${dbands.mainUrl}"],a[href^="https://i0.wp.com"]`
                  )
                  if (
                     el instanceof HTMLAnchorElement &&
                     !el.href.includes('wp-admin')
                  ) {
                     e.preventDefault()
                     this.parseUrl(el.href)
                  }
               },
               { capture: true, passive: false }
            )
         },

         updateSearch(type: string) {
            this.search.select = false
            this.search.placeholder = document
               .querySelector(`[name="${type}-placeholder"]`)
               .getAttribute('value')
            this.search.selected = document
               .querySelector(`[name="${type}-selected"]`)
               .getAttribute('value')
         },

         onInfinite() {
            this.pagination.infinite = true

            this.nextPage()
         },

         handlePopState(e: PopStateEvent) {
            if (e.state === null) {
               return
            }

            this.parseUrl(location.href, true)
         },

         parseUrl(fullUrl: string, fromHistory = false) {
            if (location.href === fullUrl && !fromHistory) {
               return
            }

            this.pagination.maxPage = 0

            const urlPatterns = {
               gallery: '\\.(jpg|jpeg|gif|png|webp|apng)$',
               author: `^author/([\\w-]+)(?:/page\\/)?(\\d+)?`,
               lyric: `^traducoes/([\\w-]+)`,
               tv: `^tv/([^"&?\/\\s]{11,34})`,
               search: `^busca/([^"&?\/\\s]+)(?:/page\\/)?(\\d+)?$`,
               search_type: `^busca/([\\w-]+)/(.+)$`,
               tag: '^' + dbands.tagBase + `/([\\w-]+)(?:/page\\/)?(\\d+)?`,
               category:
                  '^' + dbands.catBase + `/([\\w-]+)(?:/page\\/)?(\\d+)?$`,
               subcategory:
                  '^' +
                  dbands.catBase +
                  `/(?:[\\w-]+)/([\\w-]+)(?:/page\\/)?(\\d+)?`,
               single: `^\\d{4}/\\d{2}/([\\w-]+)`,
               date: `^(\\d{4}/\\d{2})(?:/page\\/)?(\\d+)?`,
               page: `^\\b(?!page)\\b([\\w-]+)\/?(?:page\\/)?(\\d+)?`,
               home: `^(?:page\\/)?(\\d+)?`,
            }

            const url = new URL(fullUrl)

            let found: string
            let slug: string
            let searchType: string
            let page: string

            Object.entries(urlPatterns).forEach(([key, pattern]) => {
               if (found) {
                  return
               }

               const path = url.pathname.slice(1)
               const matches = path.match(new RegExp(pattern))

               if (matches) {
                  found = key
                  if ('subcategory' === found) {
                     found = 'category'
                  }

                  if ('home' === found) {
                     slug = ''
                     page = matches[1] ?? '0'
                  } else {
                     slug = matches[1]
                     page = matches[2] ?? '0'
                  }
               }
            })

            if ('search' === found) {
               searchType = 'site'
            }

            if ('search_type' === found) {
               searchType = slug
               slug = page.toString()
               page = '0'
            }

            if ('gallery' === found) {
               this.openGallery(fullUrl)
               return
            }

            if ('tv' === found) {
               this.tv.play(slug, 'full')
               return
            }

            if (searchType) {
               this.$rest
                  .get(`${dbands.apiBase}/search`, {
                     page,
                     search_type: searchType,
                     q: slug,
                  })
                  .then((response) => {
                     this.postAjax(page, fullUrl, fromHistory, response.success)
                  })
               return
            }

            this.$rest
               .get(`${dbands.apiBase}/ajax`, {
                  key: found,
                  value: slug,
                  page,
               })
               .then((response) => {
                  this.postAjax(page, fullUrl, fromHistory, response.success)

                  if (!url?.hash.length) {
                     return
                  }

                  this.$nextTick(() => {
                     this.$do('scroll', url.hash)
                  })
               })
         },

         postAjax(
            page: string,
            fullUrl: string,
            fromHistory: boolean,
            success: boolean
         ) {
            if ('0' !== page || !success) {
               return
            }

            if (!fromHistory) {
               history.pushState({}, '', fullUrl)
            }

            this.tv.parent.url = fullUrl
            this.pagination.currentPage = 1

            this.$nextTick(() => {
               this.pagination.archiveUrl =
                  this.$refs.archiveLink?.dataset.archiveLink ?? dbands.mainUrl
            })
         },

         handleSearch(el: HTMLFormElement) {
            const data = new FormData(el)
            let s = data.get('s')
            s = encodeURI(s.toString())
            let type = data.get('search_type')
            if ('site' === type) {
               type = ''
            } else {
               type += '/'
            }

            this.parseUrl(dbands.mainUrl + '/busca/' + type + s)
         },

         checkCurrentPage() {
            const root = document.querySelector(':root')
            const breakpoint =
               Number(
                  getComputedStyle(root)
                     .getPropertyValue('--breakpoint-lg')
                     .replace('rem', '')
               ) * 16

            if (breakpoint > window.innerWidth) {
               return
            }

            const pages = document.getElementsByClassName('container-content')

            if (pages.length < 2) {
               return
            }

            let biggestView = 0
            let biggestEl: Element

            for (const el of pages) {
               const rect = el.getBoundingClientRect()

               const visibleHeight = Math.max(
                  0,
                  Math.min(rect.bottom, window.innerHeight) -
                     Math.max(rect.top, 0)
               )

               if (0 === visibleHeight || visibleHeight < biggestView) {
                  continue
               }

               biggestView = visibleHeight
               biggestEl = el
            }

            if (biggestEl !== this.pagination.currentEl) {
               this.pagination.currentEl = biggestEl
               biggestEl
                  .querySelector('aside')
                  .appendChild(document.getElementById('aside'))
            }
         },

         handleScroll() {
            this.checkCurrentPage()

            if (false === this.pagination.infinite) {
               return
            }

            if (document.body.classList.contains('cav-body-loading')) {
               return
            }

            if (this.pagination.currentPage >= this.pagination.maxPage) {
               return
            }

            const triggerPoint =
               document.getElementById('footer').getBoundingClientRect().top +
               window.scrollY

            if (triggerPoint - window.scrollY >= 600) {
               return
            }

            this.nextPage()
         },

         nextPage() {
            if (this.pagination.maxPage <= this.pagination.currentPage) {
               return
            }
            this.pagination.currentPage++

            this.parseUrl(
               this.pagination.archiveUrl +
                  '/page/' +
                  this.pagination.currentPage
            )
         },

         openGallery(currentImage: string) {
            this.gallery.open = true
            this.gallery.images = []

            document
               .querySelectorAll('#content main a[href*="wp-content/uploads"]')
               .forEach((img) => {
                  this.gallery.images.push(img.getAttribute('href'))
               })

            this.gallery.current = this.gallery.images.findIndex(
               (img) => img === currentImage
            )
         },

         navGallery(e: boolean | WheelEvent | TouchEvent = true) {
            if (this.gallery.images.length < 2) {
               return
            }

            if (e instanceof TouchEvent) {
               if (null === this.gallery.lastX) {
                  this.gallery.lastX = e.changedTouches[0].clientX
                  return
               }
               const diff = e.changedTouches[0].clientX - this.gallery.lastX
               this.gallery.lastX = e.changedTouches[0].clientX
               if (Math.abs(diff) < 100) {
                  return
               }
               e = diff > 0
            }

            if (e instanceof WheelEvent) {
               e = e.deltaY > 0
            }

            if (e) {
               if (this.gallery.current === this.gallery.images.length - 1) {
                  this.gallery.current = 0
               } else {
                  this.gallery.current++
               }
            } else {
               if (this.gallery.current === 0) {
                  this.gallery.current = this.gallery.images.length - 1
               } else {
                  this.gallery.current--
               }
            }

            document
               .querySelectorAll(`#galleryImages li.galleryImage`)
               [this.gallery.current].scrollIntoView({
                  behavior: 'smooth',
                  inline: 'center',
               })
         },

         async shareCopy(text: string) {
            let el = document.querySelector(
               'li.share-copy div button'
            ) as HTMLElement

            if (!el) {
               return
            }

            let oldText = el.innerText
            await this.$do({ action: 'copy', content: text })

            el.innerText = 'Link copiado!'
            setTimeout(() => {
               el.innerText = oldText
            }, 2000)
         },

         showShare() {
            this.$do([
               {
                  action: 'remClass',
                  target: '.share-hidden',
                  content: 'share-hidden',
               },
               { action: 'hide', target: '.share-show' },
               { action: 'scroll', target: '#share' },
            ])
         },
      }
   })
})

Alpine.start()
