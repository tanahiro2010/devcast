const HomeHeader = () => (
  <header className="w-full h-16 border-b border-white/10 flex items-center justify-center px-4 sm:px-6 md:px-10">
    <div className="w-full max-w-4xl flex items-center justify-between gap-4">
      <h1 className="text-[15px] font-semibold tracking-tight text-white truncate pl-10 md:pl-0">記事一覧</h1>
      <a
        href="/articles/new"
        className="shrink-0 bg-white text-neutral-900 text-[13px] font-medium px-3.5 sm:px-5 py-2.5 rounded-md hover:bg-neutral-200 transition whitespace-nowrap"
      >
        ＋ <span className="hidden sm:inline">新規記事</span>
      </a>
    </div>
  </header>
)

export { HomeHeader }
