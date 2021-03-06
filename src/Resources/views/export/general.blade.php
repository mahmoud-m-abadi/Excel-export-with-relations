<table border="1">
    <thead>
    <tr>
        @foreach($generateHeaders as $headKey => $headValue)
            @continue($headKey == 'id')
            <th @if(count($relationHeaders)) rowspan="2" @endif>
                @if(is_array($headValue))
                    {{$headValue['name'] ?? $headKey}}
                @else
                    {{$headValue}}
                @endif
            </th>
        @endforeach
        @foreach($relationHeaders as $headRelationKey => $headRelationValues)
            <th valign="center" colspan="{{count($headRelationValues)}}">
                {{$headRelationKey}}
            </th>
        @endforeach
    </tr>
    @if(count($relationHeaders))
        <tr>
            @foreach($relationHeaders as $headRelationKey => $headRelationValues)
                @foreach($headRelationValues as $headRelationKey => $headRelationValue)
                    <th >
                        @if(is_array($headRelationValue))
                            {{$headRelationValue['name']}}
                        @else
                            {{$headRelationValue}}
                        @endif
                    </th>
                @endforeach
            @endforeach
        </tr>
    @endif
    </thead>
    <tbody>
    @foreach($model as $row)
        @php
            $countRelations = $row->tdValues['relations_count'];
            $i = 0;
            $checkOdd = $loop->odd;
        @endphp
        @if(count($row->tdValues['relations']))
            @foreach($row->tdValues['relations'] as $valueRelation)
                <tr @if($checkOdd) style="background-color: darkgray" @endif>
                    @if($i == 0)
                        @foreach($row->tdValues as $tdKey => $tdValue)
                            @continue($tdKey == 'id' or $tdKey == 'relations_count' or $tdKey == 'relations')
                            <td rowspan="{{$countRelations}}">{{$tdValue}}</td>
                        @endforeach
                    @endif
                    @php
                        $i++;
                    @endphp


                    @foreach($valueRelation as $valueRKey => $valueRValue)
                        @foreach($valueRValue as $relationHeaderField)
                            <td >{{$relationHeaderField}}</td>
                        @endforeach
                    @endforeach
                </tr>
            @endforeach
        @else
            <tr>
                @foreach($row->tdValues as $tdKey => $tdValue)
                    @continue($tdKey == 'id' or $tdKey == 'relations_count' or $tdKey == 'relations')
                    <td>{{$tdValue}}</td>
                @endforeach
            </tr>
        @endif

    @endforeach
    </tbody>
</table>
