---
to: <%= pagePath %>
---
<%_ if (params.length > 0) { -%>
type Props = {
  params: {
    <%_ params.forEach(({ name, type, optional }) => { -%>
    <%= optional ? `${name}?` : `${name}` %>: <%= type %>;
    <%_ }) -%>
  }
}

<%_ } -%>
<%_ if (params.length > 0) { -%>
export default function <%= Name %>({ params }: Props) {
<%_ } else { -%>
export default function <%= Name %>() {
<%_ } -%>
  return (
    <div>Find me in <%= pagePath %></div>
  )
}
