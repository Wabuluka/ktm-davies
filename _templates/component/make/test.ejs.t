---
to: "<%= withJest ? `${path}/index.test.tsx` : null %>"
---
import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { <%= Name %> } from './'

const user = userEvent.setup()

beforeEach(() => {
  render(<<%= Name %> />)
})

test.todo('<%= Name %> のテストを作成する')
