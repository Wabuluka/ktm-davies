import { NextRequest, NextResponse } from 'next/server'
import { NextApiResponse } from 'next/types'

type BasicAuthArgs = {
  rewriteTo: string
  req: NextRequest
}

export const basicAuth = ({ rewriteTo, req }: BasicAuthArgs) => {
  const basicAuth = req.headers.get('authorization')
  const url = req.nextUrl

  if (basicAuth) {
    const authValue = basicAuth.split(' ')[1]
    const [user, password] = atob(authValue).split(':')

    if (
      user === process.env.BASIC_AUTH_USER &&
      password === process.env.BASIC_AUTH_PASSWORD
    ) {
      return NextResponse.next()
    }
  }
  url.pathname = rewriteTo

  return NextResponse.rewrite(url)
}

export const authFailureResponse = (res: NextApiResponse) => {
  res.setHeader('WWW-authenticate', 'Basic realm="Secure Area"')
  res.statusCode = 401
  res.end(`Auth Required.`)
}
