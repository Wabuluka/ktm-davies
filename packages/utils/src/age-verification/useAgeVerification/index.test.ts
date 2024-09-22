import { act, renderHook } from '@testing-library/react'
import { useAgeVerification } from '.'

const now = 1482363367071
const before7days = now - 1000 * 60 * 60 * 24 * 7
const before2hours = now - 1000 * 60 * 60 * 2
const before3days3hours = now - (1000 * 60 * 60 * 24 * 3 + 1000 * 60 * 60 * 3)
Date.now = jest.fn(() => now)

function setup({
  expires,
  lastVerifiedAt,
}: {
  expires: {
    day: number
    hour: number
  }
  lastVerifiedAt?: number
}) {
  localStorage.clear()
  if (lastVerifiedAt) {
    localStorage.setItem(
      'ageVverification_lastVerifiedAt',
      lastVerifiedAt.toString()
    )
  }
  return renderHook(() => useAgeVerification({ expires }))
}

type Result = ReturnType<typeof setup>['result']

// 期間指定がない場合
describe('最後に年齢認証した日時が見つからない場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({ expires: { day: 0, hour: 0 } }).result
  })
  test('年齢認証を求められること', () => {
    expect(result.current.verified).toBe(false)
  })
  test('認証できること', () => {
    act(() => result.current.onVerify())
    expect(result.current.verified).toBe(true)
    expect(localStorage.getItem('ageVverification_lastVerifiedAt')).toBe(
      now.toString()
    )
  })
})

// 日付指定の場合
describe('最後に年齢認証した日時が見つからない場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({ expires: { day: 7, hour: 0 } }).result
  })
  test('年齢認証を求められること', () => {
    expect(result.current.verified).toBe(false)
  })
  test('認証できること', () => {
    act(() => result.current.onVerify())
    expect(result.current.verified).toBe(true)
    expect(localStorage.getItem('ageVverification_lastVerifiedAt')).toBe(
      now.toString()
    )
  })
})

describe('最後に年齢認証を行ったのが 1 週間以上前である場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({
      expires: { day: 7, hour: 0 },
      lastVerifiedAt: before7days,
    }).result
  })
  test('年齢認証を求められること', () => {
    expect(result.current.verified).toBe(false)
  })
  test('認証できること', () => {
    act(() => result.current.onVerify())
    expect(result.current.verified).toBe(true)
    expect(localStorage.getItem('ageVverification_lastVerifiedAt')).toBe(
      now.toString()
    )
  })
})

describe('最後に年齢認証を行ったのが 1 週間以内である場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({
      expires: { day: 7, hour: 0 },
      lastVerifiedAt: before7days + 1,
    }).result
  })
  test('年齢認証済みであること', () => {
    expect(result.current.verified).toBe(true)
  })
})

//　時間指定の場合
describe('最後に年齢認証した時間が見つからない場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({ expires: { day: 0, hour: 2 } }).result
  })
  test('年齢認証を求められること', () => {
    expect(result.current.verified).toBe(false)
  })
  test('認証できること', () => {
    act(() => result.current.onVerify())
    expect(result.current.verified).toBe(true)
    expect(localStorage.getItem('ageVverification_lastVerifiedAt')).toBe(
      now.toString()
    )
  })
})

describe('最後に年齢認証を行ったのが 2 時間以上前である場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({
      expires: { day: 0, hour: 2 },
      lastVerifiedAt: before2hours,
    }).result
  })
  test('年齢認証を求められること', () => {
    expect(result.current.verified).toBe(false)
  })
  test('認証できること', () => {
    act(() => result.current.onVerify())
    expect(result.current.verified).toBe(true)
    expect(localStorage.getItem('ageVverification_lastVerifiedAt')).toBe(
      now.toString()
    )
  })
})

describe('最後に年齢認証を行ったのが 2 時間以内である場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({
      expires: { day: 0, hour: 2 },
      lastVerifiedAt: before2hours + 1,
    }).result
  })
  test('年齢認証済みであること', () => {
    expect(result.current.verified).toBe(true)
  })
})

//　日・時間どちらも指定した場合
describe('最後に年齢認証した時間が見つからない場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({ expires: { day: 3, hour: 3 } }).result
  })
  test('年齢認証を求められること', () => {
    expect(result.current.verified).toBe(false)
  })
  test('認証できること', () => {
    act(() => result.current.onVerify())
    expect(result.current.verified).toBe(true)
    expect(localStorage.getItem('ageVverification_lastVerifiedAt')).toBe(
      now.toString()
    )
  })
})

describe('最後に年齢認証を行ったのが 3 日と 3 時間以上前である場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({
      expires: { day: 3, hour: 3 },
      lastVerifiedAt: before3days3hours,
    }).result
  })
  test('年齢認証を求められること', () => {
    expect(result.current.verified).toBe(false)
  })
  test('認証できること', () => {
    act(() => result.current.onVerify())
    expect(result.current.verified).toBe(true)
    expect(localStorage.getItem('ageVverification_lastVerifiedAt')).toBe(
      now.toString()
    )
  })
})

describe('最後に年齢認証を行ったのが 3 日と 3 時間以内である場合', () => {
  let result: Result
  beforeEach(() => {
    result = setup({
      expires: { day: 3, hour: 3 },
      lastVerifiedAt: before3days3hours + 1,
    }).result
  })
  test('年齢認証済みであること', () => {
    expect(result.current.verified).toBe(true)
  })
})
