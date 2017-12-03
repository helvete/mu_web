
create function dbo.udf_HexStrToVarBin(@hexstr varchar(8000)) returns varbinary(4000)
/*
** Convert a hex string to it's varbinary equivalent value.
**
** Returns null on an invalid hex string input.
*/
as
begin

   declare @len int
   declare @pos int
   declare @d1 tinyint
   declare @d2 tinyint
   declare @value binary(1)
   declare @result varbinary(4000)

   -- Ensure we're working with lowercase hex characters.
   set @hexstr = lower(@hexstr)

   -- Remove the leading hex designator (0x) if it's there.
   if (substring(@hexstr, 1, 2) = '0x')
      set @hexstr = substring(@hexstr,3,8000)

   -- Ensure we have an even number of hex digits.
   if (len(@hexstr) % 2 = 1)
      set @hexstr = '0' + @hexstr

   -- Initialise working variables.
   set @result = 0x 
   set @pos = 1
   set @len = len(@hexstr) 

   -- Loop over string and build binary value.
   while @pos < @len
   begin
      set @d1 = ascii(substring(@hexstr, @pos, 1))
      set @d2 = ascii(substring(@hexstr, @pos + 1, 1))

      if (@d1 not between 48 and 57 and @d1 not between 97 and 102) or
         (@d2 not between 48 and 57 and @d2 not between 97 and 102) return null

      set @value = 16 * (@d1 - case when @d1 < 58 then 48 else 87 end)
                      + (@d2 - case when @d2 < 58 then 48 else 87 end)

      set @result = @result + @value
      set @pos = @pos + 2
   end

   return @result
end
go

-- Basic tests/examples.

print 'Start of tests'

print 'Valid test with 0x0102030405060708090a0b0c0d0e0f;'
print dbo.udf_HexStrToVarBin('0x0102030405060708090a0b0c0d0e0f')
print 'Valid test with 0x102030405060708090a0b0c0d0e0f0;'
print dbo.udf_HexStrToVarBin('0x102030405060708090a0b0c0d0e0f0')
print 'Valid test with 0xf1e2d3c4b5a69788796a5b4c3d2e1f;'
print dbo.udf_HexStrToVarBin('0xf1e2d3c4b5a69788796a5b4c3d2e1f')

print 'Invalid test with 0x0102030z;'
print dbo.udf_HexStrToVarBin('0x0102030z')

print 'End of tests'
go