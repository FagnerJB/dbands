export default class dbtv {
   private alpine: typeof Alpine
   public current = ''
   public status = 'hidden'
   public queue = [] as any
   private player = {} as YT.Player
   private title = ''
   private ready = false
   public showButtons = true
   private checkMouse: NodeJS.Timeout
   private lastMove = Date.now()
   private state = -2
   public showNext = false
   public parent = {
      title: window.document.title,
      url: window.location.href,
   }

   constructor(alpine: typeof Alpine) {
      this.alpine = alpine

      if (this.parent.url.startsWith(dbands.mainUrl + '/tv')) {
         this.parent.url = dbands.mainUrl + '/tv'
      }
   }

   play(id: string, status = 'full') {
      if (this.queue.includes(id) || this.current === id) {
         this.alpine.$action('toast', '', 'Já está na fila')
         return
      }

      if (this.state === YT.PlayerState.ENDED) {
         this.current = id
         return
      }

      if (
         '' !== this.current &&
         !this.isPlaylist(id) &&
         !this.isPlaylist(this.current)
      ) {
         this.queue.push(id)
         this.showNext = true
         this.alpine.$action(
            'toast',
            '',
            `Adicionado na ${this.queue.length}ª posição da fila`
         )
         return
      }

      this.status = status
      this.current = id
   }

   next() {
      if (this.isPlaylist(this.current)) {
         this.player.nextVideo()
         return
      }

      const videoID = this.queue.shift()
      if (videoID) {
         this.current = videoID
      }

      this.showNext = !!this.queue.length
   }

   playWhenAvailable(videoID: string) {
      if (0 === videoID.length) {
         return
      }

      let checkYt = setInterval(() => {
         if ('function' === typeof YT.Player) {
            clearInterval(checkYt)
            this.play(videoID, 'full')
            return
         }
      }, 111)
   }

   initPlayer(id: string) {
      let options = {} as YT.PlayerOptions
      let playerVars = {} as YT.PlayerVars
      if (this.isPlaylist(id)) {
         playerVars.list = id
         playerVars.listType = 'playlist'
         this.showNext = true
      } else {
         options.videoId = id
      }

      this.player = new YT.Player('player-container', {
         ...options,
         width: '640',
         height: '390',
         events: {
            onReady: () => this.onPlayerReady(),
            onStateChange: (e) => (this.state = e.data),
         },
         playerVars: {
            ...playerVars,
            modestbranding: 1,
            enablejsapi: 1,
            cc_load_policy: 0,
            rel: 0,
            fs: 0,
         },
      })
   }

   onStatusChange(newStatus: string) {
      if ('full' === newStatus) {
         if (this.title.length) {
            document.title = this.title + ' | Deutsche Bands'
            history.replaceState(
               { onlyReplace: true },
               '',
               dbands.mainUrl + '/tv/' + this.current
            )
         }
      } else {
         document.title = document.body.dataset.title
         history.replaceState({ onlyReplace: true }, '', this.parent.url)
      }
   }

   onCurrentChange(current: string) {
      if ('' === current) {
         if (this.ready) {
            this.player.stopVideo()
         }
         this.title = ''
         this.queue = []
         this.status = 'hidden'
         return
      }

      if (this.ready) {
         if (this.isPlaylist(current)) {
            this.player.destroy()
         } else {
            this.player.loadVideoById(current)
            return
         }
      }

      this.initPlayer(current)
   }

   onStateChange(state: number) {
      switch (state) {
         case YT.PlayerState.ENDED:
            this.next()
            break

         case YT.PlayerState.BUFFERING:
            if (0 === this.queue.length) {
               this.alpine.$wp
                  .get(`db/v1/next-video?v=${this.current}`)
                  .then((response) => {
                     if (response.data.next) {
                        this.queue.push(response.data.next)
                        this.showNext = true
                     }
                  })
            }
            break

         case YT.PlayerState.PLAYING:
            //@ts-expect-error
            this.title = this.player.getVideoData().title
            if ('full' === this.status) {
               this.onStatusChange(this.status)
            }
            break

         default:
            break
      }
   }

   onPlayerReady() {
      this.ready = true
      this.player.playVideo()
   }

   onMouseLeave() {
      this.showButtons = false
   }

   onMouseMove() {
      this.showButtons = true
      this.lastMove = Date.now()

      if (this.checkMouse) {
         clearTimeout(this.checkMouse)
      }

      this.checkMouse = setTimeout(() => {
         if (Date.now() - this.lastMove >= 3000) {
            this.showButtons = false
         }
      }, 3000)
   }

   private isPlaylist(id: string) {
      if (!id) {
         return false
      }

      return id.length >= 18
   }
}
