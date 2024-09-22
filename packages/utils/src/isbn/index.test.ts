import { convertToIsbn10 } from '.'

describe('convertToIsbn10', () => {
  test('チェックディジットが X', () => {
    expect(convertToIsbn10('9784774183619')).toBe('477418361X')
  })
  test('チェックディジット 0', () => {
    expect(convertToIsbn10('9784797386295')).toBe('4797386290')
  })
  test('チェックディジットが X と 0 以外', () => {
    expect(convertToIsbn10('9784063842760')).toBe('4063842762')
  })
  test('ISBN 13 をハイフンなしで指定しないとエラーになること', () => {
    expect(() => convertToIsbn10('4063842762')).toThrowError(
      '13 桁の ISBN をハイフンなしで指定する必要があります'
    )
    expect(() => convertToIsbn10('9784-06384276-0')).toThrowError(
      '13 桁の ISBN をハイフンなしで指定する必要があります'
    )
  })
})
