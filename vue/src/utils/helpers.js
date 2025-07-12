export function debounce(fn, delay = 300) {
  let timer = null
  return (...args) => {
    clearTimeout(timer)
    timer = setTimeout(() => {
      fn(...args)
    }, delay)
  }
}

export function stripHtml(html) {
  return html.replace(/<[^>]*>/g, '').trim()
}
