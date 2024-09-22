---
to: "<%= withStories ? `${path}/index.stories.tsx` : null %>"
---
import type { Meta, StoryObj } from '@storybook/react'
import { <%= Name %> } from './'
import { PCStory } from '@/tests/storybook'

const meta: Meta<typeof <%= Name %>>= {
  component: <%= Name %>,
  parameters: {
    ...PCStory.parameters,
  },
  tags: ['autodocs'],
}

export default meta
type Story = StoryObj<typeof <%= Name %>>

export const Default: Story = {}
