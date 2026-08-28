type Props = {
  title?: string
  message?: string
  onRetry?: () => void
}

const ErrorState = ({
  title = "読み込みに失敗しました",
  message = "時間をおいて再度お試しください。問題が解決しない場合はサポートまでご連絡ください。",
  onRetry,
}: Props) => (
  <div className="flex-1 w-full flex flex-col items-center justify-center text-center px-6 py-24">
    <div className="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-6">
      <span className="accent text-2xl leading-none">⚠</span>
    </div>
    <p className="kicker text-[11px] font-semibold accent uppercase mb-3">Error</p>
    <p className="text-white font-semibold mb-1">{title}</p>
    <p className="text-sm text-neutral-500 max-w-xs">{message}</p>
    {onRetry && (
      <button
        onClick={onRetry}
        className="mt-6 bg-white text-neutral-900 text-[13px] font-medium px-5 py-2.5 rounded-md hover:bg-neutral-200 transition"
      >
        再読み込み
      </button>
    )}
  </div>
)

export { ErrorState }
