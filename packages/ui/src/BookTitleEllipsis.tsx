'use client'

import LinesEllipsis, { ReactLinesEllipsisProps } from 'react-lines-ellipsis'
import responsiveHOC from 'react-lines-ellipsis/lib/responsiveHOC'

const ResponsiveEllipsis = responsiveHOC()(LinesEllipsis)

const omissionTargets = ['二次元コミックマガジン', '別冊コミックアンリアル']

/** 特定の文字列が含まれる場合、その文字列を省略する */
const omitBookName = (name: string) => {
  omissionTargets.forEach((omissionTarget) => {
    name = name.replaceAll(omissionTarget, '')
  })
  return name.trim()
}

type Props = {
  title: string
  volume?: string
} & ReactLinesEllipsisProps

/**
 * 書籍名を表示する
 * 書籍名に特定の文字列が含まれる場合、その文字列を省略する
 * 書籍名が特定の行数を超える場合、超えた分を省略して末尾に巻数を付け足す
 */
export function BookTitleEllipsis({ title, volume, ...props }: Props) {
  return (
    <ResponsiveEllipsis
      text={omitBookName(title || '')}
      maxLine="3"
      ellipsis={volume ? `...${volume}` : '...'}
      {...props}
    />
  )
}
