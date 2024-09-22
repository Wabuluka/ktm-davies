import { GraphQLClient } from 'graphql-request'
import 'server-only'

export const graphQLClient = new GraphQLClient(
  process.env.GRAPHQL_ENDPOINT || '',
  {
    headers: {
      authorization: `Basic ${process.env.GRAPHQL_BASIC_AUTH_BASE64} Bearer ${process.env.GRAPHQL_API_KEY}`,
    },
  }
)
