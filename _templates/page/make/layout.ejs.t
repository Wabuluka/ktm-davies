---
to: <%= layoutPath %>
---
import { PropsWithChildren } from 'react';

<%_ if (params.length > 0) { -%>
type Props = PropsWithChildren<{
  params: {
    <%_ params.forEach(({ name, type, optional }) => { -%>
    <%= optional ? `${name}?` : `${name}` %>: <%= type %>;
    <%_ }) -%>
  }
}>

<%_ } else { -%>
type Props = PropsWithChildren<{}>

<%_ } -%>
<%_ if (params.length > 0) { -%>
export default function <%= Name %>Layout({ params, children }: Props) {
<%_ } else { -%>
export default function <%= Name %>Layout({ children }: Props) {
<%_ } -%>
  return (
    <div>{children}</div>
  )
}
