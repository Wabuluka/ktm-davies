---
to: "<%= withComponent ? `${path}/index.tsx` : null %>"
---
<%_ if (hasProps) { -%>
type Props = {}

<%_ } -%>
<%_ if (hasProps) { -%>
export function <%= Name %>({}: Props) {
<%_ } else { -%>
export function <%= Name %>() {
<%_ } -%>
  return (
    <div>Find me in <%= path %></div>
  )
}
