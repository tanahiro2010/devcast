import { useState } from "react"
import { NavLink } from "react-router-dom"
import { useProfile } from "../../contexts/ProfileContext"

const navItems = [
  { label: "記事一覧", to: "/" },
  { label: "配信先設定", to: "/providers" },
  { label: "タグ管理", to: "/tags" },
  { label: "分析", to: "/analytics" },
  { label: "設定", to: "/settings" },
]

const Sidebar = () => {
  const [isOpen, setIsOpen] = useState(false)
  const profile = useProfile()

  return (
    <>
      <button
        onClick={() => setIsOpen(true)}
        className="md:hidden fixed top-3 left-3 z-20 w-10 h-10 flex items-center justify-center rounded-md text-neutral-300 hover:text-white hover:bg-white/5"
        aria-label="メニューを開く"
      >
        <span className="text-xl leading-none">☰</span>
      </button>

      {isOpen && (
        <div
          onClick={() => setIsOpen(false)}
          className="fixed inset-0 bg-black/60 z-30 md:hidden"
        />
      )}

      <aside
        className={`fixed md:static inset-y-0 left-0 z-40 w-64 md:w-60 border-r border-white/10 bg-[#0a0a0a] flex flex-col
          transition-transform duration-200 ease-out
          ${isOpen ? "translate-x-0" : "-translate-x-full"} md:translate-x-0`}
      >
        <div className="h-16 flex items-center justify-between px-6 border-b border-white/10">
          <span className="font-semibold tracking-tight text-[15px] text-white">DevCast</span>
          <button
            onClick={() => setIsOpen(false)}
            className="md:hidden text-neutral-400 hover:text-white"
            aria-label="メニューを閉じる"
          >
            ✕
          </button>
        </div>
        <nav className="flex-1 px-3 py-5 space-y-0.5 text-[13px] font-medium">
          {navItems.map((item) =>
            item.to === "#" ? (
              <a
                key={item.label}
                href={item.to}
                className="flex items-center gap-2 px-3 py-2 rounded-md text-neutral-400 hover:text-white hover:bg-white/5"
              >
                {item.label}
              </a>
            ) : (
              <NavLink
                key={item.label}
                to={item.to}
                end
                onClick={() => setIsOpen(false)}
                className={({ isActive }) =>
                  `flex items-center gap-2 px-3 py-2 rounded-md ${
                    isActive
                      ? "bg-white/5 text-white"
                      : "text-neutral-400 hover:text-white hover:bg-white/5"
                  }`
                }
              >
                {({ isActive }) => (
                  <>
                    {isActive && <span className="w-1.5 h-1.5 rounded-full bg-accent" />}
                    {item.label}
                  </>
                )}
              </NavLink>
            )
          )}
        </nav>
        <div className="p-5 border-t border-white/10">
          <p className="kicker text-[10px] uppercase text-neutral-500 mb-2">Signed in as</p>
          <p className="text-[13px] font-medium text-white">{profile.email}</p>
        </div>
      </aside>
    </>
  )
}

export { Sidebar }
